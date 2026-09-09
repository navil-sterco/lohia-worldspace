<?php
namespace App\Http\Controllers\Admin;

use App\Services\CmsHtmlParser;
use Inertia\Inertia;
use App\Models\Page;
use Illuminate\Support\Str;
use App\Models\PageSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class PageSectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-page-section')->only(['index', 'show']);
        $this->middleware('permission:create-page-section')->only(['create', 'store']);
        $this->middleware('permission:edit-page-section')->only(['edit', 'update']);
        $this->middleware('permission:delete-page-section')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $pageId = $request->input('page_id');

        $sections = PageSection::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('identifier', 'like', "%{$search}%");
        })
            ->when($pageId, function ($query, $pageId) {
                return $query->whereHas('pages', function ($query) use ($pageId) {
                    $query->where('pages.id', $pageId);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'identifier' => $section->identifier,
                    'html_template' => Str::limit($section->html_template, 100),
                    'fields_count' => collect($section->fields_config ?? [])->sum(function ($group) {
                        return count($group['fields'] ?? []);
                    }),
                    'mapping_count' => $section->mapping_enabled
                        ? collect($section->mapping_config ?? [])->sum(function ($groupOrField) {
                            if (isset($groupOrField['fields']) && is_array($groupOrField['fields'])) {
                                return count($groupOrField['fields']);
                            }
                            return 1;
                        })
                        : 0,
                    'mapping_enabled' => $section->mapping_enabled,
                    'is_active' => $section->is_active,
                    'created_at' => $section->created_at->format('M d, Y'),
                    'used_in_pages' => $section->pages()->count(),
                ];
            });

        $pages = Page::whereHas('sections')
            ->orderBy('title')
            ->get(['id', 'title']);

        return Inertia::render('PageSection/Index', [
            'sections' => $sections,
            'searchTerm' => $search ?? '',
            'pages' => $pages,
            'pageId' => $pageId ? (int) $pageId : null,
        ]);
    }

    public function create()
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/page-sections')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.page-sections' => $previousUrl]);
            }
        }

        return Inertia::render('PageSection/Create');
    }

    public function store(Request $request)
    {
        $parser = new CmsHtmlParser();
        $htmlTemplate = $request->input('html_template', '');
        if (is_string($htmlTemplate) && trim($htmlTemplate) !== '' && $parser->containsCmsAttributes($htmlTemplate)) {
            $parsed = $parser->generate($htmlTemplate);
            $request->merge([
                'html_template' => $parsed['template'],
                'fields_config' => $parsed['fields_config'],
                'mapping_config' => $parsed['mapping_config'],
                'mapping_enabled' => !empty($parsed['mapping_config']),
            ]);
        }

        $fieldsConfig = $request->input('fields_config');
        if (is_string($fieldsConfig)) {
            $decoded = json_decode($fieldsConfig, true);
            if (!is_array($decoded)) {
                return back()->withErrors([
                    'fields_config' => 'Invalid JSON format for fields configuration.'
                ])->withInput();
            }
            $request->merge(['fields_config' => $decoded]);
        }

        $mappingConfig = $request->input('mapping_config');
        if (is_string($mappingConfig)) {
            $decoded = json_decode($mappingConfig, true);
            if (!is_array($decoded)) {
                return back()->withErrors([
                    'mapping_config' => 'Invalid JSON format for mapping configuration.'
                ])->withInput();
            }
            $request->merge(['mapping_config' => $this->normalizeMappingConfigGroups($decoded)]);
        }
        if (is_array($mappingConfig)) {
            $request->merge(['mapping_config' => $this->normalizeMappingConfigGroups($mappingConfig)]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:page_sections,identifier',
            'html_template' => 'required|string',
            'fields_config' => 'nullable|array',
            'fields_config.*.name' => 'required|string',
            'fields_config.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color,link,richtext',
            'fields_config.*.label' => 'required|string',
            'fields_config.*.required' => 'boolean',
            'fields_config.*.placeholder' => 'nullable|string',
            'fields_config.*.options' => 'nullable|array',
            'fields_config.*.default' => 'nullable',
            'mapping_config' => 'nullable|array',
            'mapping_config.*.group_label' => 'required|string',
            'mapping_config.*.group_name' => 'required|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.parent_group' => 'nullable|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.fields' => 'required|array',
            'mapping_config.*.fields.*.name' => 'required|string',
            'mapping_config.*.fields.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color,link,richtext',
            'mapping_config.*.fields.*.label' => 'required|string',
            'mapping_config.*.fields.*.required' => 'boolean',
            'mapping_config.*.fields.*.options' => 'nullable|array',
            'mapping_config.*.fields.*.default' => 'nullable',
            'mapping_enabled' => 'boolean',
            'css_styles' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        PageSection::create($validated);

        $returnUrl = session()->pull('return_url.page-sections', route('page-sections.index'));
        return redirect()->to($returnUrl)->with('success', 'Page section created successfully!');
    }

    public function show(PageSection $pageSection)
    {
        return Inertia::render('PageSection/Show', [
            'section' => $pageSection->load('pages')
        ]);
    }

    public function edit(PageSection $pageSection)
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/page-sections')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.page-sections' => $previousUrl]);
            }
        }
        return Inertia::render('PageSection/Edit', [
            'section' => $pageSection
        ]);
    }

    public function update(Request $request, PageSection $pageSection)
    {
        $parser = new CmsHtmlParser();
        $htmlTemplate = $request->input('html_template', '');
        $parsedCmsConfig = null;
        if (is_string($htmlTemplate) && trim($htmlTemplate) !== '' && $parser->containsCmsAttributes($htmlTemplate)) {
            $parsedCmsConfig = $parser->generate($htmlTemplate);
            $request->merge([
                'html_template' => $parsedCmsConfig['template'],
            ]);
        }

        $rawFieldsConfig = $request->input('fields_config');
        if (is_string($rawFieldsConfig)) {
            $decoded = json_decode($rawFieldsConfig, true);
            if (!is_array($decoded)) {
                return back()->withErrors([
                    'fields_config' => 'Invalid JSON format for fields configuration.'
                ])->withInput();
            }
            $request->merge(['fields_config' => $decoded]);
        }

        $rawMappingConfig = $request->input('mapping_config');
        if (is_string($rawMappingConfig)) {
            $decoded = json_decode($rawMappingConfig, true);
            if (!is_array($decoded)) {
                return back()->withErrors([
                    'mapping_config' => 'Invalid JSON format for mapping configuration.'
                ])->withInput();
            }
            $request->merge(['mapping_config' => $this->normalizeMappingConfigGroups($decoded)]);
        }
        if (is_array($rawMappingConfig)) {
            $request->merge(['mapping_config' => $this->normalizeMappingConfigGroups($rawMappingConfig)]);
        }

        if ($parsedCmsConfig !== null) {
            $request->merge([
                'fields_config' => $this->mergeFieldConfigs(
                    $request->input('fields_config', []),
                    $parsedCmsConfig['fields_config'] ?? []
                ),
                'mapping_config' => $this->mergeMappingConfigs(
                    $request->input('mapping_config', []),
                    $parsedCmsConfig['mapping_config'] ?? []
                ),
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:page_sections,identifier,' . $pageSection->id,
            'html_template' => 'required|string',
            'fields_config' => 'nullable|array',
            'fields_config.*.name' => 'required|string',
            'fields_config.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color,link,richtext',
            'fields_config.*.label' => 'required|string',
            'fields_config.*.required' => 'boolean',
            'fields_config.*.placeholder' => 'nullable|string',
            'fields_config.*.options' => 'nullable|array',
            'fields_config.*.default' => 'nullable',
            'mapping_config' => 'nullable|array',
            'mapping_config.*.group_label' => 'required|string',
            'mapping_config.*.group_name' => 'required|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.parent_group' => 'nullable|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.fields' => 'required|array',
            'mapping_config.*.fields.*.name' => 'required|string',
            'mapping_config.*.fields.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color,link,richtext',
            'mapping_config.*.fields.*.label' => 'required|string',
            'mapping_config.*.fields.*.required' => 'boolean',
            'mapping_config.*.fields.*.options' => 'nullable|array',
            'mapping_config.*.fields.*.default' => 'nullable',
            'mapping_enabled' => 'boolean',
            'css_styles' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $pageSection->update($validated);

        $returnUrl = session()->pull('return_url.page-sections', route('page-sections.index'));
        return redirect()->to($returnUrl)->with('success', 'Page section updated successfully!');
    }

    public function destroy(PageSection $pageSection)
    {
        if ($pageSection->pages()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete section. It is being used in pages.');
        }

        $pageSection->delete();

        return back()->with('success', 'Page section deleted successfully!');
    }

    public function toggleStatus(PageSection $pageSection)
    {
        $pageSection->update([
            'is_active' => !$pageSection->is_active
        ]);

        return redirect()->back()->with('success', 'Section status updated successfully!');
    }

    private function normalizeMappingConfigGroups($mappingConfig): array
    {
        $mappingConfig = is_array($mappingConfig) ? $mappingConfig : [];
        if (empty($mappingConfig))
            return [];

        $first = $mappingConfig[0] ?? null;

        if (is_array($first) && array_key_exists('fields', $first)) {
            return array_values(array_map(function ($g) {
                $g = is_array($g) ? $g : [];
                $parent = isset($g['parent_group']) && is_string($g['parent_group']) && $g['parent_group'] !== ''
                    ? $g['parent_group']
                    : null;

                return [
                    'group_label' => $g['group_label'] ?? 'Repeatable Items',
                    'group_name' => $g['group_name'] ?? 'items',
                    'parent_group' => $parent,
                    'fields' => is_array($g['fields'] ?? null) ? $g['fields'] : [],
                    'default_items' => is_array($g['default_items'] ?? null) ? $g['default_items'] : [],
                ];
            }, $mappingConfig));
        }

        if (is_array($first) && array_key_exists('name', $first)) {
            return [
                [
                    'group_label' => 'Repeatable Items',
                    'group_name' => 'items',
                    'parent_group' => null,
                    'fields' => $mappingConfig,
                ]
            ];
        }

        return [];
    }

    private function mergeFieldConfigs($submitted, $parsed): array
    {
        $submitted = is_array($submitted) ? $submitted : [];
        $parsed = is_array($parsed) ? $parsed : [];
        $existingNames = [];

        foreach ($submitted as $field) {
            if (is_array($field) && isset($field['name'])) {
                $existingNames[$field['name']] = true;
            }
        }

        foreach ($parsed as $field) {
            if (is_array($field) && isset($field['name']) && !isset($existingNames[$field['name']])) {
                $submitted[] = $field;
                $existingNames[$field['name']] = true;
            }
        }

        return array_values($submitted);
    }

    private function mergeMappingConfigs($submitted, $parsed): array
    {
        $submitted = $this->normalizeMappingConfigGroups($submitted);
        $parsed = $this->normalizeMappingConfigGroups($parsed);
        $groupsByName = [];

        foreach ($submitted as $index => $group) {
            $groupName = $group['group_name'] ?? 'items';
            $groupsByName[$groupName] = $index;
        }

        foreach ($parsed as $parsedGroup) {
            $groupName = $parsedGroup['group_name'] ?? 'items';
            if (!isset($groupsByName[$groupName])) {
                $submitted[] = $parsedGroup;
                $groupsByName[$groupName] = count($submitted) - 1;
                continue;
            }

            $index = $groupsByName[$groupName];
            $existingFields = $submitted[$index]['fields'] ?? [];
            $existingNames = [];
            foreach ($existingFields as $field) {
                if (is_array($field) && isset($field['name'])) {
                    $existingNames[$field['name']] = true;
                }
            }

            foreach ($parsedGroup['fields'] ?? [] as $field) {
                if (is_array($field) && isset($field['name']) && !isset($existingNames[$field['name']])) {
                    $existingFields[] = $field;
                    $existingNames[$field['name']] = true;
                }
            }

            $submitted[$index]['fields'] = array_values($existingFields);
        }

        return array_values($submitted);
    }
}

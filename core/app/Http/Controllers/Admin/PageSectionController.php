<?php
namespace App\Http\Controllers\Admin;

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
            'fields_config.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color',
            'fields_config.*.label' => 'required|string',
            'fields_config.*.required' => 'boolean',
            'fields_config.*.placeholder' => 'nullable|string',
            'fields_config.*.options' => 'nullable|array',
            'mapping_config' => 'nullable|array',
            'mapping_config.*.group_label' => 'required|string',
            'mapping_config.*.group_name' => 'required|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.parent_group' => 'nullable|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.fields' => 'required|array',
            'mapping_config.*.fields.*.name' => 'required|string',
            'mapping_config.*.fields.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color',
            'mapping_config.*.fields.*.label' => 'required|string',
            'mapping_config.*.fields.*.required' => 'boolean',
            'mapping_config.*.fields.*.options' => 'nullable|array',
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|unique:page_sections,identifier,' . $pageSection->id,
            'html_template' => 'required|string',
            'fields_config' => 'nullable|array',
            'fields_config.*.name' => 'required|string',
            'fields_config.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color',
            'fields_config.*.label' => 'required|string',
            'fields_config.*.required' => 'boolean',
            'fields_config.*.placeholder' => 'nullable|string',
            'fields_config.*.options' => 'nullable|array',
            'mapping_config' => 'nullable|array',
            'mapping_config.*.group_label' => 'required|string',
            'mapping_config.*.group_name' => 'required|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.parent_group' => 'nullable|string|regex:/^[a-z][a-z0-9_]*$/',
            'mapping_config.*.fields' => 'required|array',
            'mapping_config.*.fields.*.name' => 'required|string',
            'mapping_config.*.fields.*.type' => 'required|string|in:text,textarea,number,email,url,select,checkbox,radio,file,date,image,code,color',
            'mapping_config.*.fields.*.label' => 'required|string',
            'mapping_config.*.fields.*.required' => 'boolean',
            'mapping_config.*.fields.*.options' => 'nullable|array',
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
}
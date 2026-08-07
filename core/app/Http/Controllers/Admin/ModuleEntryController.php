<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\ModuleEntry;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Http\Controllers\Controller;


class ModuleEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('module.permission:entries.view')->only(['index', 'show']);
        $this->middleware('module.permission:entries.create')->only(['create', 'store']);
        $this->middleware('module.permission:entries.update')->only(['edit', 'update']);
        $this->middleware('module.permission:entries.delete')->only(['destroy']);
        $this->middleware('module.permission:entries.detail')->only(['detail', 'storeDetail']);
        $this->middleware('module.permission:entries.mapping')->only(['mapping', 'attachMapping']);
        $this->middleware('module.permission:entries.sub-pages')->only([
            'pages',
            'storePage',
            'editPage',
            'updatePage',
            'pageSections',
            'storePageSections',
            'destroyPageSection',
            'detachPage',
        ]);
    }

    private function flattenMappingFields(array $mappingConfig): array
    {
        $groups = $this->normalizeMappingConfigGroups($mappingConfig);
        $fields = [];
        foreach ($groups as $g) {
            foreach (($g['fields'] ?? []) as $f) {
                if (is_array($f)) {
                    $fields[] = $f;
                }
            }
        }
        return $fields;
    }

    public function index(Request $request, Module $module)
    {
        $module->fields_config = $module->fields_config ?? [];
        $module->mapping_config = $module->mapping_config ?? [];
        $module->mapping_enabled = $module->mapping_enabled ?? false;

        $search = $request->input('search');
        $pageId = $request->query('page_id');
        $pageId = is_numeric($pageId) ? (int) $pageId : null;

        $pagesList = Page::select('id', 'title')
            ->where('page_type', 'modular')
            ->whereHas('moduleEntries', function ($q) use ($module) {
                $q->where('module_entries.module_id', $module->id);
            })
            ->orderBy('title')
            ->get();

        $entries = $module->entries()
            ->when($search, function ($query, $search) use ($module) {
                return $query->where(function ($q) use ($search, $module) {
                    foreach (($module->fields_config ?? []) as $field) {
                        $name = $field['name'] ?? null;
                        if (!$name)
                            continue;
                        $q->orWhere("data->$name", 'like', "%{$search}%");
                    }

                    if ($module->mapping_enabled && !empty($module->mapping_config)) {
                        foreach ($this->flattenMappingFields($module->mapping_config) as $mf) {
                            $name = $mf['name'] ?? null;
                            if ($name) {
                                $q->orWhere("data->$name", 'like', "%{$search}%");
                            }
                        }
                    }
                });
            })
            ->when($pageId, function ($query) use ($pageId) {
                return $query->whereHas('pages', function ($q) use ($pageId) {
                    $q->where('pages.id', $pageId);
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(function (ModuleEntry $entry) {
                return [
                    'id' => $entry->id,
                    'slug' => $entry->slug,
                    'data' => $entry->data ?? [],
                    'sort_order' => $entry->sort_order,
                    'is_published' => (bool) $entry->is_published,
                    'created_at' => optional($entry->created_at)?->format('M d, Y'),
                ];
            });

        $mappedModuleEntries = $this->getMappedModuleEntries($this->flattenMappingFields($module->mapping_config ?? []));

        return Inertia::render('ModuleEntry/Index', [
            'mappedModuleEntries' => $mappedModuleEntries,
            'module' => $this->modulePayloadWithSections($module),
            'entries' => $entries,
            'searchTerm' => $search ?? '',
            'pagesList' => $pagesList,
            'selectedPageId' => $pageId,
        ]);
    }

    public function create(Module $module)
    {
        $mappedModuleEntries = $this->getMappedModuleEntries($this->flattenMappingFields($module->mapping_config ?? []));

        return Inertia::render('ModuleEntry/Create', [
            'module' => $this->modulePayloadWithSections($module),
            'mappedModuleEntries' => $mappedModuleEntries,
        ]);
    }

    public function store(Request $request, Module $module)
    {
        $fieldsConfig = $module->fields_config ?? [];

        $rules = $this->buildRulesFromConfig(
            $fieldsConfig,
            (bool) ($module->mapping_enabled ?? false),
            $module->mapping_config ?? [],
            (bool) ($module->types_enabled ?? false),
            $module->types ?? [],
            $module->selectbox_module_ids ?? []
        );

        $hasSlugField = collect($fieldsConfig)->contains(function ($field) {
            return !empty($field['is_slug']);
        });

        $rules['slug'] = $hasSlugField
            ? [
                'required',
                'string',
                'max:255',
                'regex:/^\/?[a-z0-9]+(?:[-\/][a-z0-9]+)*$/i',
                function ($attribute, $value, $fail) use ($module) {
                    if (ModuleEntry::where('module_id', $module->id)->where('slug', $value)->exists()) {
                        $fail('This slug already exists for this module.');
                    }
                },
            ]
            : [
                'nullable',
                'string',
                'max:255',
                'regex:/^\/?[a-z0-9]+(?:[-\/][a-z0-9]+)*$/i',
                function ($attribute, $value, $fail) use ($module) {
                    if ($value && ModuleEntry::where('module_id', $module->id)->where('slug', $value)->exists()) {
                        $fail('This slug already exists for this module.');
                    }
                },
            ];

        foreach ($fieldsConfig as $field) {
            $name = $field['name'] ?? null;
            if (!$name)
                continue;
            if (in_array($field['type'] ?? 'text', ['file', 'image'])) {
                $rules["data.{$name}"] = 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,avi,mov,ico,pdf,doc,docx|max:5000';
            }
        }

        $mappingGroups = $this->normalizeMappingConfigGroups($module->mapping_config ?? []);
        foreach ($mappingGroups as $group) {
            $groupName = $group['group_name'] ?? 'items';
            foreach (($group['fields'] ?? []) as $field) {
                $name = $field['name'] ?? null;
                if (!$name)
                    continue;
                if (in_array($field['type'] ?? 'text', ['file', 'image'])) {
                    $rules["mapping_data.{$groupName}.*.{$name}"] = 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,avi,mov,ico,pdf,doc,docx|max:5000';
                }
            }
        }

        $validated = $request->validate($rules);

        $data = $validated['data'] ?? [];
        foreach ($fieldsConfig as $field) {
            $name = $field['name'] ?? null;
            if (!$name)
                continue;
            if (in_array($field['type'] ?? 'text', ['file', 'image']) && $request->hasFile("data.{$name}")) {
                $file = $request->file("data.{$name}");
                $data[$name] = $this->uploadFile($file, $module->id);
            }
        }
        if ((bool) ($module->mapping_enabled ?? false)) {
            $mappingGroups = $this->normalizeMappingConfigGroups($module->mapping_config ?? []);
            $itemsByGroup = [];
            foreach ($mappingGroups as $group) {
                $groupName = $group['group_name'] ?? 'items';
                $items = $validated['mapping_data'][$groupName] ?? [];
                $items = is_array($items) ? $items : [];
                foreach ($items as $i => $item) {
                    $item = is_array($item) ? $item : [];
                    foreach (($group['fields'] ?? []) as $field) {
                        $fname = $field['name'] ?? null;
                        if (!$fname)
                            continue;
                        if (in_array($field['type'] ?? 'text', ['file', 'image']) && $request->hasFile("mapping_data.{$groupName}.{$i}.{$fname}")) {
                            $uploaded = $this->uploadFile($request->file("mapping_data.{$groupName}.{$i}.{$fname}"), $module->id);
                            $item[$fname] = $uploaded;
                        }
                    }
                    $items[$i] = $item;
                }
                $itemsByGroup[$groupName] = array_values($items);
            }
            $data['mapping_items'] = $itemsByGroup;
        }
        if ((bool) ($module->types_enabled ?? false) && isset($validated['type'])) {
            $data['type'] = $validated['type'];
        }

        $slug = $validated['slug'] ?? null;
        if (!$slug && (bool) ($module->auto_generate_slug ?? false)) {
            $firstTextField = collect($fieldsConfig)->first(function ($field) {
                return in_array($field['type'] ?? 'text', ['text', 'textarea']);
            });
            if ($firstTextField) {
                $fieldName = $firstTextField['name'];
                $textValue = $data[$fieldName] ?? '';
                if ($textValue) {
                    $baseSlug = Str::slug($textValue);
                    $slug = $baseSlug;
                    $count = 1;
                    while (ModuleEntry::where('module_id', $module->id)->where('slug', $slug)->exists()) {
                        $slug = "{$baseSlug}-" . $count++;
                    }
                }
            }
        }


        $module->entries()->create([
            'slug' => strtolower($slug),
            'data' => $data,
            'sort_order' => (int) ($request->input('sort_order', 0)),
            'is_published' => (bool) $request->boolean('is_published', false),
        ]);

        return redirect()
            ->route('modules.entries.index', $module->id)
            ->with('success', 'Entry added successfully!');
    }

    public function show(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        return Inertia::render('ModuleEntry/Show', [
            'module' => $this->modulePayloadWithSections($module),
            'entry' => [
                'id' => $entry->id,
                'slug' => $entry->slug,
                'data' => $entry->data ?? [],
                'sort_order' => $entry->sort_order,
                'is_published' => (bool) $entry->is_published,
                'created_at' => optional($entry->created_at)?->format('M d, Y'),
            ],
        ]);
    }

    public function edit(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $mappedModuleEntries = $this->getMappedModuleEntries($this->flattenMappingFields($module->mapping_config ?? []));

        return Inertia::render('ModuleEntry/Edit', [
            'module' => $this->modulePayloadWithSections($module),
            'entry' => [
                'id' => $entry->id,
                'slug' => $entry->slug,
                'data' => $entry->data ?? [],
                'sort_order' => $entry->sort_order,
                'is_published' => (bool) $entry->is_published,
            ],
            'mappedModuleEntries' => $mappedModuleEntries,
        ]);
    }

    public function update(Request $request, Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $fieldsConfig = $module->fields_config ?? [];

        $rules = $this->buildRulesFromConfig(
            $fieldsConfig,
            (bool) ($module->mapping_enabled ?? false),
            $module->mapping_config ?? [],
            (bool) ($module->types_enabled ?? false),
            $module->types ?? [],
            $module->selectbox_module_ids ?? []
        );

        $hasSlugField = collect($fieldsConfig)->contains(function ($field) {
            return !empty($field['is_slug']);
        });

        $rules['slug'] = $hasSlugField
            ? [
                'required',
                'string',
                'regex:/^\/?[a-z0-9]+(?:[-\/][a-z0-9]+)*$/i',
                'max:255',
                function ($attribute, $value, $fail) use ($module, $entry) {
                    if (
                        ModuleEntry::where('module_id', $module->id)
                            ->where('slug', $value)
                            ->where('id', '!=', $entry->id)
                            ->exists()
                    ) {
                        $fail('This slug already exists for this module.');
                    }
                },
            ]
            : [
                'nullable',
                'string',
                'regex:/^\/?[a-z0-9]+(?:[-\/][a-z0-9]+)*$/i',
                'max:255',
                function ($attribute, $value, $fail) use ($module, $entry) {
                    if (
                        $value &&
                        ModuleEntry::where('module_id', $module->id)
                            ->where('slug', $value)
                            ->where('id', '!=', $entry->id)
                            ->exists()
                    ) {
                        $fail('This slug already exists for this module.');
                    }
                },
            ];

        foreach ($fieldsConfig as $field) {
            $name = $field['name'] ?? null;
            if (!$name)
                continue;
            if (in_array($field['type'] ?? 'text', ['file', 'image'])) {
                $rules["data.{$name}"] = [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value === null || $value === '') {
                            return;
                        }
                        if (is_string($value)) {
                            return;
                        }
                        if ($value instanceof \Illuminate\Http\UploadedFile) {
                            if (
                                !in_array($value->getMimeType(), [
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                    'image/svg+xml',
                                    'image/webp',
                                    'video/mp4',
                                    'video/x-msvideo',
                                    'video/quicktime',
                                    'image/x-icon',
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                ])
                            ) {
                                $fail("The {$attribute} field has an invalid file type.");
                            } elseif ($value->getSize() > 5000 * 1024) {
                                $fail("The {$attribute} field must not exceed 5000 KB.");
                            }
                            return;
                        }
                        $fail("The {$attribute} field must be a file or string.");
                    }
                ];
            }
        }

        $mappingGroups = $this->normalizeMappingConfigGroups($module->mapping_config ?? []);
        foreach ($mappingGroups as $group) {
            $groupName = $group['group_name'] ?? 'items';
            foreach (($group['fields'] ?? []) as $field) {
                $name = $field['name'] ?? null;
                if (!$name)
                    continue;
                if (in_array($field['type'] ?? 'text', ['file', 'image'])) {
                    $rules["mapping_data.{$groupName}.*.{$name}"] = [
                        'nullable',
                        function ($attribute, $value, $fail) {
                            if ($value === null || $value === '')
                                return;
                            if (is_string($value))
                                return;
                            if ($value instanceof \Illuminate\Http\UploadedFile) {
                                if (
                                    !in_array($value->getMimeType(), [
                                        'image/jpeg',
                                        'image/png',
                                        'image/gif',
                                        'image/svg+xml',
                                        'image/webp',
                                        'video/mp4',
                                        'video/x-msvideo',
                                        'video/quicktime',
                                        'image/x-icon',
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                    ])
                                )
                                    $fail("The {$attribute} field has an invalid file type.");
                                elseif ($value->getSize() > 5000 * 1024)
                                    $fail("The {$attribute} field must not exceed 5000 KB.");
                                return;
                            }
                            $fail("The {$attribute} field must be a file or string.");
                        }
                    ];
                }
            }
        }

        $validated = $request->validate($rules);

        // Parse deleted files list
        $deletedFiles = [];
        if ($request->has('_deleted_files')) {
            $deletedFiles = json_decode($request->input('_deleted_files', '[]'), true) ?? [];
        }

        // Parse explicitly cleared (nulled/emptied) fields
        $clearedFields = [];
        if ($request->has('_cleared_fields')) {
            $clearedFields = json_decode($request->input('_cleared_fields', '[]'), true) ?? [];
        }

        $data = $validated['data'] ?? [];

        foreach ($fieldsConfig as $field) {
            $name = $field['name'] ?? null;
            if (!$name)
                continue;

            $value = $data[$name] ?? null;
            $isFileType = in_array($field['type'] ?? 'text', ['file', 'image']);

            if ($isFileType) {
                if (in_array($name, $deletedFiles)) {
                    // Explicitly deleted file — clear it
                    $data[$name] = '';
                } elseif ($value instanceof \Illuminate\Http\UploadedFile) {
                    // New file uploaded — store it
                    $filePath = $this->uploadFile($value, $module->id);
                    $data[$name] = $filePath;
                } elseif (is_string($value) && !empty($value)) {
                    // Existing file path string passed back — keep it
                    $data[$name] = $value;
                } else {
                    // Nothing submitted — preserve old value
                    $data[$name] = $entry->data[$name] ?? '';
                }
            } else {
                if (in_array($name, $clearedFields)) {
                    // Field was intentionally cleared by the user
                    $data[$name] = null;
                } elseif (array_key_exists($name, $validated['data'] ?? [])) {
                    // Field was submitted (even if empty string) — save as-is
                    $data[$name] = $validated['data'][$name];
                } else {
                    // Field was not submitted at all — preserve old value
                    $data[$name] = $entry->data[$name] ?? '';
                }
            }
        }

        if ((bool) ($module->mapping_enabled ?? false)) {
            $mappingGroups = $this->normalizeMappingConfigGroups($module->mapping_config ?? []);
            $existingByGroup = $this->normalizeMappingItemsByGroup($entry->data['mapping_items'] ?? []);
            $incomingByGroup = $validated['mapping_data'] ?? [];
            $incomingByGroup = is_array($incomingByGroup) ? $incomingByGroup : [];
            $itemsByGroup = [];

            foreach ($mappingGroups as $group) {
                $groupName = $group['group_name'] ?? 'items';
                $items = $incomingByGroup[$groupName] ?? [];
                $items = is_array($items) ? array_values($items) : [];
                foreach ($items as $i => $item) {
                    $item = is_array($item) ? $item : [];
                    foreach (($group['fields'] ?? []) as $field) {
                        $fname = $field['name'] ?? null;
                        if (!$fname)
                            continue;
                        if (in_array($field['type'] ?? 'text', ['file', 'image'])) {
                            $key = "mapping_data.{$groupName}.{$i}.{$fname}";
                            if ($request->hasFile($key)) {
                                $item[$fname] = $this->uploadFile($request->file($key), $module->id);
                            } elseif (!empty($existingByGroup[$groupName][$i][$fname] ?? '') && empty($item[$fname] ?? '')) {
                                $item[$fname] = $existingByGroup[$groupName][$i][$fname];
                            }
                        }
                    }
                    $items[$i] = $item;
                }
                $itemsByGroup[$groupName] = $items;
            }

            $data['mapping_items'] = $itemsByGroup;
        }

        if ((bool) ($module->types_enabled ?? false) && isset($validated['type'])) {
            $data['type'] = $validated['type'];
        }

        if (isset($entry->data['sections'])) {
            $data['sections'] = $entry->data['sections'];
        }

        $slug = $validated['slug'] ?? ($entry->slug ?: null);
        if (!$slug && (bool) ($module->auto_generate_slug ?? false)) {
            $firstTextField = collect($fieldsConfig)->first(function ($field) {
                return in_array($field['type'] ?? 'text', ['text', 'textarea']);
            });
            if ($firstTextField) {
                $fieldName = $firstTextField['name'];
                $textValue = $data[$fieldName] ?? '';
                if ($textValue) {
                    $baseSlug = Str::slug($textValue);
                    $slug = $baseSlug;
                    $count = 1;
                    while (ModuleEntry::where('module_id', $module->id)->where('slug', $slug)->where('id', '!=', $entry->id)->exists()) {
                        $slug = "{$baseSlug}-" . $count++;
                    }
                }
            }
        }

        $entry->update([
            'slug' => strtolower($slug),
            'data' => $data,
            'sort_order' => (int) ($request->input('sort_order', $entry->sort_order)),
            'is_published' => (bool) $request->boolean('is_published', $entry->is_published),
        ]);

        // Keep module sub page slugs in sync with current entry slug prefix
        $entry->load('subPages');
        $entryPrefix = $entry->slug ?: ('entry-' . $entry->id);
        $entry->subPages
            ->where('page_type', 'module_sub_page')
            ->each(function ($page) use ($entryPrefix) {
                $existingPageSlug = trim((string) ($page->slug ?? ''));
                $childSlug = trim((string) Str::afterLast($existingPageSlug, '/'));
                if ($childSlug === '') {
                    $childSlug = Str::slug((string) ($page->title ?? ''));
                }
                if ($childSlug === '') {
                    return;
                }
                $page->update(['slug' => trim($entryPrefix, '/') . '/' . trim($childSlug, '/')]);
            });

        return redirect()
            ->route('modules.entries.index', $module->id)
            ->with('success', 'Entry updated successfully!');
    }

    public function destroy(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        if ($entry->slug) {
            $entry->slug = 'trash-' . $entry->slug . '-' . $entry->id;
            $entry->save();
        }

        $entry->pages()->detach();

        $entry->load('subPages');
        foreach ($entry->subPages as $subPage) {
            $entry->subPages()->detach($subPage->id);
            $subPage->delete();
        }

        DB::table('module_entry_mapping')
            ->where('module_entry_id', $entry->id)
            ->orWhere('related_module_entry_id', $entry->id)
            ->delete();

        $entry->delete();

        return back()->with('success', 'Entry deleted successfully!');
    }

    public function detail(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);
        $pageSectionIds = $module->page_section_ids ?? [];
        if (empty($pageSectionIds)) {
            return redirect()->route('modules.entries.index', $module->id)
                ->with('error', 'This module has no page sections configured.');
        }

        $payload = $this->modulePayloadWithSections($module);
        $sectionData = $entry->data['sections'] ?? [];

        // Fetch the PageSection models for these section_ids to format active sections
        $sectionIds = collect($sectionData)->pluck('section_id')->toArray();
        $sectionsMap = PageSection::whereIn('id', $sectionIds)->get()->keyBy('id');

        $activeSections = [];
        foreach ($sectionData as $index => $sd) {
            $sid = $sd['section_id'] ?? null;
            if (!$sid)
                continue;
            $sec = $sectionsMap->get($sid);
            if (!$sec)
                continue;

            $activeSections[] = [
                'id' => $sec->id,
                'name' => $sec->name,
                'identifier' => $sec->identifier,
                'fields_config' => $sec->fields_config ?? [],
                'mapping_config' => $sec->mapping_config ?? [],
                'mapping_enabled' => (bool) ($sec->mapping_enabled ?? false),
                'pivot' => [
                    'id' => null, // Omitted database record ID so client-side delete removes from state only
                    'order' => $index,
                    'section_data' => json_encode([
                        'data' => $sd['data'] ?? [],
                        'mapping_items' => $sd['mapping_items'] ?? [],
                        'is_hidden' => (bool) ($sd['is_hidden'] ?? false),
                    ]),
                ]
            ];
        }

        return Inertia::render('ModuleEntry/Detail', [
            'module' => ['id' => $module->id, 'name' => $module->name],
            'entry' => [
                'id' => $entry->id,
                'slug' => $entry->slug,
                'slug_prefix' => $this->entrySlugPrefix($entry, $module),
                'is_published' => (bool) $entry->is_published,
            ],
            'activeSections' => $activeSections,
            'sections' => $payload['sections'],
            'entryLabel' => $this->getEntryLabel($entry->data ?? [], $module->fields_config ?? [], $entry->id),
        ]);
    }

    public function storeDetail(Request $request, Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);
        $pageSectionIds = array_map('intval', $module->page_section_ids ?? []);
        if (empty($pageSectionIds)) {
            return redirect()->route('modules.entries.index', $module->id)->with('error', 'Module has no sections.');
        }

        $request->validate([
            'sections' => 'required|array',
            'sections.*.section_id' => 'required|integer|in:' . implode(',', $pageSectionIds),
            'sections.*.order' => 'required|integer',
            'sections.*.section_data' => 'required|string',
            'sections.*.files' => 'sometimes|array',
            'sections.*.mapping_files' => 'sometimes|array',
        ]);

        $sectionsToSave = [];
        foreach ($request->sections as $index => $sectionReq) {
            $sectionId = (int) $sectionReq['section_id'];
            $parsedData = json_decode($sectionReq['section_data'], true) ?? [];

            // Handle flat files — use isset() on the merged array, not hasFile()
            if (isset($sectionReq['files']) && is_array($sectionReq['files'])) {
                foreach ($sectionReq['files'] as $fieldName => $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $parsedData['data'][$fieldName] = $this->uploadFile($file, $module->id);
                    }
                }
            }

            // Handle mapping files (including nested structures)
            if (isset($sectionReq['mapping_files']) && is_array($sectionReq['mapping_files'])) {
                $mf = $sectionReq['mapping_files'];
                foreach ($mf as $groupName => $itemsByIndex) {
                    if (!is_array($itemsByIndex))
                        continue;
                    foreach ($itemsByIndex as $itemIndex => $itemFiles) {
                        if (!is_array($itemFiles))
                            continue;
                        foreach ($itemFiles as $fieldName => $value) {
                            if ($value instanceof \Illuminate\Http\UploadedFile) {
                                $parsedData['mapping_items'][$groupName][$itemIndex][$fieldName]
                                    = $this->uploadFile($value, $module->id);
                            } elseif (is_array($value)) {
                                foreach ($value as $nestedIndex => $nestedFiles) {
                                    if (!is_array($nestedFiles))
                                        continue;
                                    foreach ($nestedFiles as $nestedField => $maybeFile) {
                                        if ($maybeFile instanceof \Illuminate\Http\UploadedFile) {
                                            $parsedData['mapping_items'][$groupName][$itemIndex][$fieldName][$nestedIndex][$nestedField]
                                                = $this->uploadFile($maybeFile, $module->id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $sectionsToSave[] = [
                'section_id' => $sectionId,
                'data' => $parsedData['data'] ?? [],
                'mapping_items' => $parsedData['mapping_items'] ?? [],
                'is_hidden' => (bool) ($parsedData['is_hidden'] ?? false),
            ];
        }

        $data = is_array($entry->data) ? $entry->data : [];
        $data['sections'] = $sectionsToSave;
        $entry->data = $data;
        $entry->save();

        return redirect()
            ->route('modules.entries.detail', ['module' => $module->id, 'entry' => $entry->id])
            ->with('success', 'Sections saved successfully!');
    }

    public function mapping(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $entry->load(['pages:id,title,slug', 'relatedEntries']);

        $lists = [];

        // Build Pages list with "Home" as the first item
        $homeItem = (object) ['id' => 'home', 'title' => 'Home', 'slug' => '/'];
        $pages = Page::select('id', 'title', 'slug')->where('page_type', 'modular')->get();
        $pagesWithHome = collect([$homeItem])->concat($pages);
        $lists[] = [
            'field' => 'page_ids',
            'label' => 'Pages',
            'icon' => '📄',
            'items' => $pagesWithHome->values(),
            'displayField' => 'title',
            'relationshipKey' => 'pages',
            'colClass' => 'col-lg-4 col-md-6 mb-3',
        ];

        $moduleSubPages = Page::select('id', 'title', 'slug')->where('page_type', 'module_sub_page')->get();
        $lists[] = [
            'field' => 'page_ids',
            'label' => 'Module Sub Pages',
            'icon' => '📄',
            'items' => $moduleSubPages,
            'displayField' => 'title',
            'relationshipKey' => 'pages',
            'colClass' => 'col-lg-4 col-md-6 mb-3',
        ];

        $mapToModuleIds = $module->map_to_module_ids ?? [];
        $mappableModules = Module::whereIn('id', $mapToModuleIds)->get()->keyBy('id');

        foreach ($mapToModuleIds as $mid) {
            $m = $mappableModules->get($mid);
            if (!$m)
                continue;

            $moduleEntries = ModuleEntry::where('module_id', $mid)
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'module_id', 'data']);

            $items = [];
            foreach ($moduleEntries as $me) {
                $label = $this->getEntryLabel($me->data ?? [], $m->fields_config ?? [], $me->id);
                $items[] = ['id' => $me->id, 'title' => $label];
            }

            $fieldKey = 'module_' . $mid . '_ids';
            $relatedIds = $entry->relatedEntries()->where('module_id', $mid)->pluck('module_entries.id')->toArray();

            $lists[] = [
                'field' => $fieldKey,
                'label' => $m->name,
                'icon' => '📋',
                'items' => $items,
                'displayField' => 'title',
                'relationshipKey' => 'related_' . $mid,
                'colClass' => 'col-lg-4 col-md-6 mb-3',
            ];
        }

        $entity = $entry->toArray();
        // Build pages array — include virtual 'home' ID if mapped_to_homepage is true
        $mappedPageIds = $entry->pages->map(fn($p) => ['id' => $p->id]);
        if ((bool) $entry->mapped_to_homepage) {
            $mappedPageIds = collect([['id' => 'home']])->concat($mappedPageIds);
        }
        $entity['pages'] = $mappedPageIds->values();
        foreach ($mapToModuleIds as $mid) {
            $entity['related_' . $mid] = $entry->relatedEntries()->where('module_id', $mid)->get()->map(fn($e) => ['id' => $e->id]);
        }

        return Inertia::render('ModuleEntry/Mapping', [
            'module' => ['id' => $module->id, 'name' => $module->name],
            'entry' => $entity,
            'entryLabel' => $this->getEntryLabel($entry->data ?? [], $module->fields_config ?? [], $entry->id),
            'lists' => $lists,
        ]);
    }

    public function pages(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $pagesPayload = $this->buildEntryPagesPayload($module, $entry);

        return Inertia::render('ModuleEntry/Pages', [
            'module' => ['id' => $module->id, 'name' => $module->name],
            'entry' => $pagesPayload['entry'],
            'entryLabel' => $pagesPayload['entryLabel'],
            'attachedPages' => $pagesPayload['attachedPages'],
            'tabs' => $pagesPayload['tabs'],
            'hasModuleSections' => $pagesPayload['hasModuleSections'],
        ]);
    }

    public function storePage(Request $request, Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'display_location' => 'nullable|in:header,footer,sidebar',
            'is_published' => 'boolean',
            'target_blank' => 'boolean',
            'tab_id' => 'nullable|exists:tabs,id',
            'display_order' => 'integer',
        ]);

        $entryPrefix = $this->entrySlugPrefix($entry, $module);
        $childSlug = $this->normalizeChildSlug($validated['slug'] ?? null, $validated['title']);
        if ($childSlug === '') {
            return back()->withErrors(['slug' => 'Could not generate a valid page slug.'])->withInput();
        }

        $fullSlug = $this->composeEntryPageSlug($entryPrefix, $childSlug);

        if (Page::query()->where('page_type', 'module_sub_page')->where('slug', $fullSlug)->exists()) {
            return back()->withErrors(['slug' => 'This page slug already exists for this entry.'])->withInput();
        }

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $fullSlug,
            'page_type' => 'module_sub_page',
            'display_location' => $validated['display_location'] ?? null,
            'is_published' => $validated['is_published'] ?? true,
            'target_blank' => $validated['target_blank'] ?? false,
            'tab_id' => $validated['tab_id'] ?? null,
            'display_order' => $validated['display_order'] ?? 1000,
        ]);

        $entry->subPages()->attach($page->id);

        return redirect()
            ->route('modules.entries.pages.sections', ['module' => $module->id, 'entry' => $entry->id, 'page' => $page->id])
            ->with('success', 'Page created successfully! Add sections below.');
    }

    public function editPage(Module $module, ModuleEntry $entry, Page $page)
    {
        $this->assertEntrySubPage($module, $entry, $page);

        $entryPrefix = $this->entrySlugPrefix($entry, $module);
        $childSlug = $this->extractChildSlug((string) ($page->slug ?? ''), $entryPrefix);

        return Inertia::render('ModuleEntry/PageEdit', [
            'module' => ['id' => $module->id, 'name' => $module->name],
            'entry' => [
                'id' => $entry->id,
                'slug' => $entry->slug,
                'slug_prefix' => $entryPrefix,
            ],
            'entryLabel' => $this->getEntryLabel($entry->data ?? [], $module->fields_config ?? [], $entry->id),
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $childSlug,
                'full_slug' => $page->slug,
                'display_location' => $page->display_location,
                'is_published' => (bool) $page->is_published,
                'target_blank' => (bool) $page->target_blank,
                'tab_id' => $page->tab_id,
                'display_order' => $page->display_order,
            ],
            'tabs' => Tab::orderBy('display_order')->get(['id', 'heading']),
        ]);
    }

    public function updatePage(Request $request, Module $module, ModuleEntry $entry, Page $page)
    {
        $this->assertEntrySubPage($module, $entry, $page);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'display_location' => 'nullable|in:header,footer,sidebar',
            'is_published' => 'boolean',
            'target_blank' => 'boolean',
            'tab_id' => 'nullable|exists:tabs,id',
            'display_order' => 'integer',
        ]);

        $entryPrefix = $this->entrySlugPrefix($entry, $module);
        $childSlug = $this->normalizeChildSlug($validated['slug'] ?? null, $validated['title']);
        if ($childSlug === '') {
            return back()->withErrors(['slug' => 'Could not generate a valid page slug.'])->withInput();
        }

        $fullSlug = $this->composeEntryPageSlug($entryPrefix, $childSlug);

        if (
            Page::query()
                ->where('page_type', 'module_sub_page')
                ->where('slug', $fullSlug)
                ->where('id', '!=', $page->id)
                ->exists()
        ) {
            return back()->withErrors(['slug' => 'This page slug already exists for this entry.'])->withInput();
        }

        $page->update([
            'title' => $validated['title'],
            'slug' => $fullSlug,
            'display_location' => $validated['display_location'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'target_blank' => $validated['target_blank'] ?? false,
            'tab_id' => $validated['tab_id'] ?? null,
            'display_order' => $validated['display_order'] ?? 1000,
        ]);

        return redirect()
            ->route('modules.entries.pages', ['module' => $module->id, 'entry' => $entry->id])
            ->with('success', 'Page updated successfully!');
    }

    public function pageSections(Module $module, ModuleEntry $entry, Page $page)
    {
        $this->assertEntrySubPage($module, $entry, $page);

        $pageSectionIds = $module->page_section_ids ?? [];
        if (empty($pageSectionIds)) {
            return redirect()
                ->route('modules.entries.pages', ['module' => $module->id, 'entry' => $entry->id])
                ->with('error', 'This module has no page sections configured.');
        }

        $page->load([
            'sections' => function ($query) {
                $query->orderBy('order');
            }
        ]);

        $activeSections = $page->sections->map(function ($section) {
            $section->fields_config = $section->fields_config ?? [];
            $section->mapping_config = $section->mapping_config ?? [];
            $section->mapping_enabled = $section->mapping_enabled ?? false;
            return $section;
        });

        $sections = PageSection::active()
            ->whereIn('id', $pageSectionIds)
            ->get(['id', 'name', 'identifier', 'fields_config', 'mapping_config', 'mapping_enabled']);

        return Inertia::render('Page/Sections', [
            'page' => $page,
            'activeSections' => $activeSections,
            'sections' => $sections,
            'backUrl' => route('modules.entries.pages', ['module' => $module->id, 'entry' => $entry->id]),
            'sectionRoutes' => [
                'store' => route('modules.entries.pages.sections.store', [
                    'module' => $module->id,
                    'entry' => $entry->id,
                    'page' => $page->id,
                ]),
                'destroy' => 'modules.entries.pages.sections.destroy',
                'destroyParams' => [
                    'module' => $module->id,
                    'entry' => $entry->id,
                ],
            ],
        ]);
    }

    public function storePageSections(Request $request, Module $module, ModuleEntry $entry, Page $page)
    {
        $this->assertEntrySubPage($module, $entry, $page);

        $allowedIds = array_map('intval', $module->page_section_ids ?? []);
        if (empty($allowedIds)) {
            return back()->with('error', 'This module has no page sections configured.');
        }

        $request->validate([
            'sections' => 'required|array|min:1',
            'sections.*.section_id' => 'required|integer|in:' . implode(',', $allowedIds),
        ]);

        return app(SectionBuilderController::class)->storeSections($request, $page->id);
    }

    public function destroyPageSection(Module $module, ModuleEntry $entry, int $pivot)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $row = DB::table('page_section')->where('id', $pivot)->first();
        if (!$row) {
            abort(404);
        }

        $page = Page::find($row->page_id);
        if (!$page) {
            abort(404);
        }

        $this->assertEntrySubPage($module, $entry, $page);

        return app(SectionBuilderController::class)->destroySection($pivot);
    }

    public function detachPage(Module $module, ModuleEntry $entry, Page $page)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $isAttached = $entry->subPages()
            ->where('pages.id', $page->id)
            ->where('pages.page_type', 'module_sub_page')
            ->exists();

        if (!$isAttached) {
            return back()->withErrors(['page_id' => 'This page is not attached to the selected entry.']);
        }

        $entry->subPages()->detach($page->id);
        $page->delete();

        return redirect()
            ->route('modules.entries.pages', ['module' => $module->id, 'entry' => $entry->id])
            ->with('success', 'Page deleted successfully!');
    }

    public function attachMapping(Request $request, Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $rules = ['page_ids' => 'nullable|array'];
        $mapToModuleIds = $module->map_to_module_ids ?? [];
        foreach ($mapToModuleIds as $mid) {
            $rules['module_' . $mid . '_ids'] = 'nullable|array';
            $rules['module_' . $mid . '_ids.*'] = 'exists:module_entries,id';
        }

        $validated = $request->validate($rules);

        // Separate the virtual 'home' ID from real page IDs
        $allPageIds = $validated['page_ids'] ?? [];
        $mappedToHomepage = in_array('home', $allPageIds, true);
        $realPageIds = array_values(array_filter($allPageIds, fn($id) => $id !== 'home'));

        $entry->update(['mapped_to_homepage' => $mappedToHomepage]);
        $entry->pages()->sync($realPageIds);

        $allRelatedIds = [];
        foreach ($mapToModuleIds as $mid) {
            $ids = $validated['module_' . $mid . '_ids'] ?? [];
            $allRelatedIds = array_merge($allRelatedIds, $ids);
        }
        $entry->relatedEntries()->sync(array_unique($allRelatedIds));

        return redirect()
            ->route('modules.entries.index', $module->id)
            ->with('success', 'Mapping saved successfully!');
    }

    private function buildRulesFromConfig(array $fieldsConfig, bool $mappingEnabled, array $mappingConfig, bool $typesEnabled = false, array $types = [], array $selectboxModuleIds = []): array
    {
        $rules = [
            'data' => 'required|array',
        ];

        if ($typesEnabled && count($types) > 0) {
            $rules['type'] = 'required|string|in:' . implode(',', $types);
        }

        foreach ($fieldsConfig as $field) {
            $name = $field['name'] ?? null;
            if (!$name)
                continue;

            $required = (bool) ($field['required'] ?? false);
            $type = $field['type'] ?? 'text';

            $rule = $required ? 'required' : 'nullable';

            if (in_array($type, ['file', 'image'], true)) {
                $rule .= '|string';
            } elseif (in_array($type, ['number'], true)) {
                $rule .= '|numeric';
            } elseif (in_array($type, ['email'], true)) {
                $rule .= '|email';
            } elseif (in_array($type, ['url'], true)) {
                $rule .= '|url';
            } elseif (in_array($type, ['checkbox'], true)) {
                $rule .= '|string';
            } else {
                $rule .= '|string';
            }

            $rules["data.{$name}"] = $rule;
        }

        if (!empty($selectboxModuleIds)) {
            $slugs = Module::whereIn('id', $selectboxModuleIds)->pluck('slug')->toArray();
            foreach ($slugs as $slug) {
                $rules["data.{$slug}"] = 'nullable|string';
            }
        }

        if ($mappingEnabled) {
            $rules['mapping_data'] = 'nullable|array';
            $groups = $this->normalizeMappingConfigGroups($mappingConfig);
            foreach ($groups as $group) {
                $groupName = $group['group_name'] ?? 'items';
                $rules["mapping_data.{$groupName}"] = 'nullable|array';
                $rules["mapping_data.{$groupName}.*"] = 'nullable|array';
                foreach (($group['fields'] ?? []) as $field) {
                    $name = $field['name'] ?? null;
                    if (!$name)
                        continue;
                    $rules["mapping_data.{$groupName}.*.{$name}"] = 'nullable|string';
                }
            }
        }

        return $rules;
    }

    private function getMappedModuleEntries(array $mappingConfig): array
    {
        $moduleIds = [];
        foreach ($mappingConfig as $field) {
            $mid = $field['source_module_id'] ?? null;
            if ($mid && is_numeric($mid)) {
                $moduleIds[] = (int) $mid;
            }
        }
        $moduleIds = array_unique($moduleIds);
        if (empty($moduleIds)) {
            return [];
        }

        $result = [];
        $modules = Module::whereIn('id', $moduleIds)->get()->keyBy('id');
        $entries = ModuleEntry::whereIn('module_id', $moduleIds)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($moduleIds as $mid) {
            $module = $modules->get($mid);
            $moduleEntries = $entries->where('module_id', $mid);
            $items = [];
            foreach ($moduleEntries as $entry) {
                $label = $this->getEntryLabel($entry->data ?? [], $module->fields_config ?? [], $entry->id);
                $items[] = ['id' => (string) $entry->id, 'label' => $label];
            }
            $result[$mid] = $items;
        }

        return $result;
    }

    private function getEntryLabel(array $data, array $fieldsConfig, int $entryId): string
    {
        foreach ($fieldsConfig as $f) {
            $name = $f['name'] ?? null;
            if (!$name)
                continue;
            $v = $data[$name] ?? null;
            if ($v !== null && $v !== '') {
                return is_string($v) ? $v : json_encode($v);
            }
        }
        return 'Entry #' . $entryId;
    }

    private function uploadFile($file, $moduleId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = 'module_' . time() . '_' . uniqid() . '.' . $extension;

        $folder = 'assets/img/modules/' . $moduleId . '/';
        $path = public_path($folder);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file->move($path, $filename);

        return asset($folder . $filename);
    }

    private function modulePayloadWithSections(Module $module): array
    {
        $selectboxModuleIds = $module->selectbox_module_ids ?? [];
        $selectboxModulesData = [];
        if (!empty($selectboxModuleIds)) {
            $selectboxModules = Module::whereIn('id', $selectboxModuleIds)->get()->keyBy('id');
            $selectboxEntries = ModuleEntry::whereIn('module_id', $selectboxModuleIds)
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
            foreach ($selectboxModuleIds as $mid) {
                $mObj = $selectboxModules->get($mid);
                if (!$mObj)
                    continue;
                $mEntries = $selectboxEntries->where('module_id', $mid);
                $items = [];
                foreach ($mEntries as $entry) {
                    $label = $this->getEntryLabel($entry->data ?? [], $mObj->fields_config ?? [], $entry->id);
                    $items[] = ['id' => (string) $entry->id, 'label' => $label];
                }
                $selectboxModulesData[] = [
                    'id' => $mid,
                    'name' => $mObj->name,
                    'slug' => $mObj->slug,
                    'options' => $items,
                ];
            }
        }

        $payload = [
            'id' => $module->id,
            'name' => $module->name,
            'slug' => $module->slug,
            'fields_config' => $module->fields_config ?? [],
            'mapping_enabled' => (bool) ($module->mapping_enabled ?? false),
            'mapping_config' => $module->mapping_config ?? [],
            'types_enabled' => (bool) ($module->types_enabled ?? false),
            'types' => $module->types ?? [],
            'selectbox_module_ids' => $selectboxModuleIds,
            'selectbox_modules_data' => $selectboxModulesData,
        ];
        $pageSectionIds = $module->page_section_ids ?? [];
        if (!empty($pageSectionIds)) {
            $sectionsById = PageSection::whereIn('id', $pageSectionIds)
                ->get(['id', 'name', 'identifier', 'fields_config', 'mapping_config', 'mapping_enabled'])
                ->keyBy('id');
            $payload['sections'] = array_values(array_filter(array_map(function ($id) use ($sectionsById) {
                return $sectionsById->get($id)?->toArray();
            }, $pageSectionIds)));
        } else {
            $payload['sections'] = [];
        }
        return $payload;
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

    private function normalizeMappingItemsByGroup($mappingItems): array
    {
        if (is_array($mappingItems) && $this->isAssocArray($mappingItems)) {
            return $mappingItems;
        }
        if (is_array($mappingItems)) {
            return ['items' => $mappingItems];
        }
        return [];
    }

    private function isAssocArray(array $arr): bool
    {
        if ($arr === [])
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function buildEntryPagesPayload(Module $module, ModuleEntry $entry): array
    {
        $entryPrefix = $this->entrySlugPrefix($entry, $module);

        $entry->load([
            'subPages' => function ($q) {
                $q->where('pages.page_type', 'module_sub_page')
                    ->withCount('sections')
                    ->orderBy('display_order')
                    ->orderBy('title');
            },
        ]);

        $attachedPages = $entry->subPages
            ->map(function ($page) use ($entryPrefix) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'child_slug' => $this->extractChildSlug((string) ($page->slug ?? ''), $entryPrefix),
                    'display_order' => $page->display_order,
                    'target_blank' => (bool) $page->target_blank,
                    'is_published' => (bool) $page->is_published,
                    'sections_count' => $page->sections_count ?? 0,
                ];
            })
            ->values();

        return [
            'entry' => [
                'id' => $entry->id,
                'slug' => $entry->slug,
                'slug_prefix' => $entryPrefix,
                'is_published' => (bool) $entry->is_published,
            ],
            'entryLabel' => $this->getEntryLabel($entry->data ?? [], $module->fields_config ?? [], $entry->id),
            'attachedPages' => $attachedPages,
            'tabs' => Tab::orderBy('display_order')->get(['id', 'heading']),
            'hasModuleSections' => !empty($module->page_section_ids),
        ];
    }

    private function assertEntrySubPage(Module $module, ModuleEntry $entry, Page $page): void
    {
        abort_unless((int) $entry->module_id === (int) $module->id, 404);
        abort_unless($page->page_type === 'module_sub_page', 404);
        abort_unless(
            $entry->subPages()->where('pages.id', $page->id)->exists(),
            404
        );
    }

    private function entrySlugPrefix(ModuleEntry $entry, Module $module): string
    {
        $prefix = trim((string) ($entry->slug ?: ''));
        if ($prefix === '') {
            $prefix = Str::slug($this->getEntryLabel($entry->data ?? [], $module->fields_config ?? [], $entry->id));
        }
        if ($prefix === '') {
            $prefix = 'entry-' . $entry->id;
        }

        return trim($prefix, '/');
    }

    private function normalizeChildSlug(?string $slug, string $title): string
    {
        $childSlug = trim((string) ($slug ?? ''));
        if ($childSlug === '') {
            return Str::slug($title);
        }

        $childSlug = trim((string) Str::afterLast($childSlug, '/'), '/');
        if (Str::startsWith($childSlug, '#')) {
            return $childSlug;
        }

        return Str::slug($childSlug) ?: $childSlug;
    }

    private function composeEntryPageSlug(string $entryPrefix, string $childSlug): string
    {
        return trim($entryPrefix, '/') . '/' . trim($childSlug, '/');
    }

    private function extractChildSlug(string $fullSlug, string $entryPrefix): string
    {
        $fullSlug = trim($fullSlug, '/');
        $entryPrefix = trim($entryPrefix, '/');
        if ($entryPrefix !== '' && Str::startsWith($fullSlug, $entryPrefix . '/')) {
            return Str::after($fullSlug, $entryPrefix . '/');
        }

        return trim((string) Str::afterLast($fullSlug, '/'), '/');
    }

    public function togglePublish(Module $module, ModuleEntry $entry)
    {
        abort_unless((int) $entry->module_id === $module->id, 404);

        $entry->update(['is_published' => !$entry->is_published]);

        return redirect()
            ->back()
            ->with('success', 'Entry ' . ($entry->is_published ? 'Published' : 'Draft') . ' successfully!');
    }
}
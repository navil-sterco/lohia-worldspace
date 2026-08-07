<?php
namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class SectionBuilderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-section')->only(['sections']);
        $this->middleware('permission:create-section')->only(['storeSections']);
        $this->middleware('permission:delete-section')->only(['destroySection']);
    }

    public function sections(Request $request, $pageId)
    {
        $page = Page::findOrFail($pageId);
        $page->load(['sections' => function($query) {
            $query->orderBy('order');
        }]);

        $activeSections = $page->sections->map(function($section) {
            $section->fields_config = $section->fields_config ?? [];
            $section->mapping_config = $section->mapping_config ?? [];
            $section->mapping_enabled = $section->mapping_enabled ?? false;
            return $section;
        });

        $sections = PageSection::active()->get(['id', 'name', 'identifier', 'fields_config', 'mapping_config', 'mapping_enabled']);

        return Inertia::render('Page/Sections', [
            'page' => $page,
            'activeSections' => $activeSections,
            'sections' => $sections,
        ]);
    }

    public function storeSections(Request $request, $pageId)
    {
        $page = Page::findOrFail($pageId);
        
        $request->validate([
            'sections' => 'required|array|min:1',
            'sections.*.section_id' => 'required|exists:page_sections,id',
            'sections.*.pivot_id' => 'nullable|integer',
            'sections.*.order' => 'required|integer',
            'sections.*.section_data' => 'required|string',
            'sections.*.files' => 'sometimes|array',
            'sections.*.mapping_files' => 'sometimes|array',
            'sections.*.files.*' => 'file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,avi,mov,ico,pdf,doc,docx|max:25000',
        ]);

        try {
            foreach ($request->sections as $index => $sectionData) {
                $parsedData = json_decode($sectionData['section_data'], true);
                
                if (isset($sectionData['files'])) {
                    foreach ($sectionData['files'] as $fieldName => $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $filePath = $this->uploadFile($file, $page->id);
                            $parsedData['data'][$fieldName] = $filePath;
                        }
                    }
                }
                
                if (isset($sectionData['mapping_files'])) {
                    $this->validateNestedMappingFiles($sectionData['mapping_files']);

                    $mf = $sectionData['mapping_files'];
                    $keys = is_array($mf) ? array_keys($mf) : [];
                    $isLegacy = !empty($keys) && is_numeric($keys[0]);

                    if ($isLegacy) {
                        foreach ($mf as $itemIndex => $itemFiles) {
                            foreach (($itemFiles ?? []) as $fieldName => $file) {
                                if ($file instanceof \Illuminate\Http\UploadedFile) {
                                    $filePath = $this->uploadFile($file, $page->id);
                                    if (!isset($parsedData['mapping_items']) || !is_array($parsedData['mapping_items'])) {
                                        $parsedData['mapping_items'] = [];
                                    }
                                    $parsedData['mapping_items'][$itemIndex][$fieldName] = $filePath;
                                }
                            }
                        }
                    } else {
                        foreach (($mf ?? []) as $groupName => $itemsByIndex) {
                            if (!is_array($itemsByIndex)) {
                                continue;
                            }
                            foreach ($itemsByIndex as $itemIndex => $itemFiles) {
                                $this->mergeGroupedMappingItemFiles($parsedData, (string) $groupName, $itemIndex, $itemFiles, $page->id);
                            }
                        }
                    }
                }

                if (!empty($sectionData['pivot_id'])) {
                    DB::table('page_section')
                        ->where('id', $sectionData['pivot_id'])
                        ->where('page_id', $page->id)
                        ->update([
                            'page_section_id' => $sectionData['section_id'],
                            'order' => $sectionData['order'],
                            'section_data' => json_encode($parsedData),
                        ]);
                } else {
                    $page->sections()->attach($sectionData['section_id'], [
                        'order' => $sectionData['order'],
                        'section_data' => json_encode($parsedData),
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Sections saved successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Error saving sections: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error saving sections: ' . $e->getMessage());
        }
    }

    /**
     * Merge uploaded files for one parent mapping row (supports nested repeater file fields).
     *
     * @param  array<string, mixed>  $parsedData
     * @param  array<string, mixed>|\Illuminate\Http\UploadedFile|null  $itemFiles
     */
    private function mergeGroupedMappingItemFiles(array &$parsedData, string $groupName, $itemIndex, $itemFiles, int $pageId): void
    {
        if (!is_array($itemFiles)) {
            return;
        }

        if (!isset($parsedData['mapping_items']) || !is_array($parsedData['mapping_items'])) {
            $parsedData['mapping_items'] = [];
        }
        if (!isset($parsedData['mapping_items'][$groupName]) || !is_array($parsedData['mapping_items'][$groupName])) {
            $parsedData['mapping_items'][$groupName] = [];
        }
        if (!isset($parsedData['mapping_items'][$groupName][$itemIndex]) || !is_array($parsedData['mapping_items'][$groupName][$itemIndex])) {
            $parsedData['mapping_items'][$groupName][$itemIndex] = [];
        }

        foreach ($itemFiles as $fieldName => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $parsedData['mapping_items'][$groupName][$itemIndex][$fieldName] = $this->uploadFile($value, $pageId);

                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            foreach ($value as $nestedIndex => $nestedFiles) {
                if (!is_array($nestedFiles)) {
                    continue;
                }
                foreach ($nestedFiles as $nestedField => $maybeFile) {
                    if (!$maybeFile instanceof \Illuminate\Http\UploadedFile) {
                        continue;
                    }
                    if (!isset($parsedData['mapping_items'][$groupName][$itemIndex][$fieldName]) || !is_array($parsedData['mapping_items'][$groupName][$itemIndex][$fieldName])) {
                        $parsedData['mapping_items'][$groupName][$itemIndex][$fieldName] = [];
                    }
                    if (!isset($parsedData['mapping_items'][$groupName][$itemIndex][$fieldName][$nestedIndex]) || !is_array($parsedData['mapping_items'][$groupName][$itemIndex][$fieldName][$nestedIndex])) {
                        $parsedData['mapping_items'][$groupName][$itemIndex][$fieldName][$nestedIndex] = [];
                    }
                    $parsedData['mapping_items'][$groupName][$itemIndex][$fieldName][$nestedIndex][$nestedField] = $this->uploadFile($maybeFile, $pageId);
                }
            }
        }
    }

    private function uploadFile($file, $pageId)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = 'section_' . time() . '_' . uniqid() . '.' . $extension;
        
        $folder = 'assets/img/pages/' . $pageId . '/';
        $path = public_path($folder);
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        $file->move($path, $filename);
        
        return asset($folder . $filename);
    }

    /**
     * Validate nested mapping files recursively.
     *
     * @param mixed $mappingFiles
     */
    private function validateNestedMappingFiles($mappingFiles): void
    {
        $leafFiles = [];
        $this->collectUploadedFiles($mappingFiles, $leafFiles);

        foreach ($leafFiles as $file) {
            Validator::make(
                ['file' => $file],
                ['file' => 'file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,avi,mov,ico,pdf,doc,docx|max:25000']
            )->validate();
        }
    }

    /**
     * @param mixed $node
     * @param array<int, \Illuminate\Http\UploadedFile> $leafFiles
     */
    private function collectUploadedFiles($node, array &$leafFiles): void
    {
        if ($node instanceof \Illuminate\Http\UploadedFile) {
            $leafFiles[] = $node;
            return;
        }

        if (!is_array($node)) {
            return;
        }

        foreach ($node as $value) {
            $this->collectUploadedFiles($value, $leafFiles);
        }
    }

    public function destroySection($id)
    {
        DB::table('page_section')->where('id', $id)->delete();
        return back()->with('success', 'Section deleted successfully!');
    }
}
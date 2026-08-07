<?php
namespace App\Models;

use App\Models\ModuleEntry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'meta_description',
        'is_published',
        'target_blank',
        'page_type',
        'display_location',
        'tab_id',
        'display_order',
        'parent_page_id'
    ];

    protected $casts = [
        'display_location' => 'array',
        'is_published' => 'boolean'
    ];

    public function sections()
    {
        return $this->belongsToMany(PageSection::class, 'page_section')->active()->withPivot('id', 'order', 'section_data')->withTimestamps()->orderBy('order');
    }

    public function tab()
    {
        return $this->belongsTo(Tab::class);
    }

    public function parentPage()
    {
        return $this->belongsTo(self::class, 'parent_page_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_page_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_page_id');
    }

    public function childPages()
    {
        return $this->hasMany(self::class, 'parent_page_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function getRenderedHtml()
    {
        $html = '';

        foreach ($this->sections as $section) {
            $sectionHtml = $section->html_template;
            $sectionData = json_decode($section->pivot->section_data, true) ?? [];

            $fieldMap = $this->fieldsForTemplateReplacement($sectionData);
            if ($fieldMap !== []) {
                $sectionHtml = $this->applySectionDataFieldReplacements($sectionHtml, $fieldMap);
            }

            $mappingItemsByGroup = $this->normalizeMappingItemsByGroup($sectionData);
            if (!empty($mappingItemsByGroup)) {
                $sectionHtml = $this->renderRepeatableGroups($sectionHtml, $mappingItemsByGroup);
            }

            $html .= $sectionHtml . "\n";
        }

        return $html;
    }

    public function getSectionHtml($section)
    {
        $sectionHtml = $section->html_template;
        $sectionData = json_decode($section->pivot->section_data, true) ?? [];

        $fieldMap = $this->fieldsForTemplateReplacement($sectionData);

        if ($fieldMap !== []) {
            $sectionHtml = $this->applySectionDataFieldReplacements($sectionHtml, $fieldMap);
        }

        $mappingItemsByGroup = $this->normalizeMappingItemsByGroup($sectionData);

        if (!empty($mappingItemsByGroup)) {
            $sectionHtml = $this->renderRepeatableGroups($sectionHtml, $mappingItemsByGroup);
        }

        $sectionHtml = preg_replace('/\{[^}]+\}/', '', $sectionHtml);

        return $sectionHtml;
    }

    public function getModularPageData()
    {
        $this->load([
            'tab',
            'parentPage.tab',
            'moduleEntries.module',
            'moduleEntries.relatedEntries.module',
            'sections' => function ($query) {
                $query->orderBy('order');
            }
        ]);

        $sectionsData = $this->sections->values()->mapWithKeys(function ($section, $index) {
            return [
                $section->identifier . '_' . $index => $this->getSectionHtml($section)
            ];
        })->toArray();

        $mapped = [];


        foreach ($this->moduleEntries as $entry) {
            if ($entry->module && $entry->module->slug) {

                $slug = $entry->module->slug;

                if (!isset($mapped[$slug])) {
                    $mapped[$slug] = [];
                }

                $entryData = $entry->data ?? [];

                $entryData['slug'] = $entry->slug;
                unset($entryData['sections']);

                $mapped[$slug][] = $entryData;
            }

            foreach ($entry->relatedEntries as $related) {
                if ($related->module && $related->module->slug) {

                    $relatedSlug = $related->module->slug;

                    if (!isset($mapped[$relatedSlug])) {
                        $mapped[$relatedSlug] = [];
                    }

                    $relatedData = $related->data ?? [];

                    $relatedData['slug'] = $related->slug;
                    unset($relatedData['sections']);
                    $mapped[$relatedSlug][] = $relatedData;
                }
            }
        }

        foreach ($mapped as $slug => $items) {

            usort($items, function ($a, $b) {

                $aOrder = $a['display_order'] ?? PHP_INT_MAX;
                $bOrder = $b['display_order'] ?? PHP_INT_MAX;

                return $aOrder <=> $bOrder;
            });

            $mapped[$slug] = $items;
        }

        $tabContextPage = $this->resolveTabContextPage();
        $tabName = $tabContextPage?->tab?->heading ?? null;
        $tab_subheading = $tabContextPage?->tab?->subheading ?? null;

        $tabs = Page::whereNotNull('tab_id')
            ->whereNull('parent_page_id')
            ->where('tab_id', $tabContextPage?->tab_id)
            ->with('tab')
            ->published()
            ->orderBy('display_order')
            ->get()
            ->groupBy(fn($page) => $page->tab?->heading ?? null)
            ->map(function ($pages) {
                return $pages->map(function ($page) {
                    return [
                        'slug' => $page->slug,
                        'title' => $page->title,
                    ];
                })->values()->toArray();
            })
            ->first();

        return [
            'tab_title' => $tabName ?? null,
            'tab_subheading' => $tab_subheading ?? null,
            'page_title' => $this->title,
            'page_slug' => $this->resolveActiveTabSlug(),
            'tab_group' => $this->resolveTabGroup(),
            'tabs' => $tabs,
            'cms' => $sectionsData,
            'modular' => $mapped,
        ];
    }

    public function getCmsPageData()
    {
        $this->loadMissing(['tab', 'parentPage.tab']);

        $sectionsData = $this->sections
            ->values()
            ->mapWithKeys(function ($section, $index) {
                return [
                    $section->identifier . '_' . $index => $this->getSectionHtml($section)
                ];
            })
            ->toArray();

        $tabContextPage = $this->resolveTabContextPage();
        $tabName = $tabContextPage?->tab?->heading ?? null;
        $tab_subheading = $tabContextPage?->tab?->subheading ?? null;

        $tabs = Page::whereNotNull('tab_id')
            ->whereNull('parent_page_id')
            ->where('tab_id', $tabContextPage?->tab_id)
            ->with('tab')
            ->published()
            ->orderBy('display_order')
            ->get()
            ->groupBy(fn($page) => $page->tab?->heading ?? null)
            ->map(function ($pages) {
                return $pages->map(function ($page) {
                    return [
                        'slug' => $page->slug,
                        'title' => $page->title,
                    ];
                })->values()->toArray();
            })
            ->first();

        return [
            'tab_title' => $tabName ?? null,
            'tab_subheading' => $tab_subheading ?? null,
            'page_title' => $this->title,
            'page_slug' => $this->resolveActiveTabSlug(),
            'tab_group' => $this->resolveTabGroup(),
            'tabs' => $tabs,
            'sections' => $sectionsData,
        ];
    }

    public function moduleEntries(): BelongsToMany
    {
        return $this->belongsToMany(ModuleEntry::class, 'module_entry_page', 'page_id', 'module_entry_id')->withTimestamps();
    }

    public function subPageEntries(): BelongsToMany
    {
        return $this->belongsToMany(ModuleEntry::class, 'module_entry_sub_pages', 'page_id', 'module_entry_id')
            ->withTimestamps();
    }

    private function resolveTabContextPage(): self
    {
        return $this->parentPage ?: $this;
    }

    private function resolveActiveTabSlug(): ?string
    {
        return $this->resolveTabContextPage()?->slug;
    }

    private function resolveTabGroup(): ?string
    {
        return $this->resolveTabContextPage()?->slug;
    }

    /**
     * Build the map used for {{field}} / {field} replacement: section_data.data plus scalar root keys (e.g. class_data).
     *
     * @param  array<string, mixed>  $sectionData
     * @return array<string, mixed>
     */
    private function fieldsForTemplateReplacement(array $sectionData): array
    {
        $out = (isset($sectionData['data']) && is_array($sectionData['data']))
            ? $sectionData['data']
            : [];
        foreach ($sectionData as $key => $value) {
            if ($key === 'data' || $key === 'mapping_items') {
                continue;
            }
            if (!is_string($key) || $key === '') {
                continue;
            }
            if (is_array($value)) {
                continue;
            }
            if (!array_key_exists($key, $out)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     * Replace field placeholders in the HTML template. Supports both {{field}} and {field}.
     * Empty or null values become an empty string so class attributes do not show a literal placeholder.
     *
     * @param  array<string, mixed>  $data
     */
    private function applySectionDataFieldReplacements(string $sectionHtml, array $data): string
    {
        foreach ($data as $key => $value) {
            if (!is_string($key) || $key === '') {
                continue;
            }
            $repl = ($value === null || $value === '')
                ? ''
                : (is_array($value) ? json_encode($value) : (string) $value);
            $sectionHtml = str_replace('{{' . $key . '}}', $repl, $sectionHtml);
            $sectionHtml = str_replace('{' . $key . '}', $repl, $sectionHtml);
        }

        return $sectionHtml;
    }

    private function normalizeMappingItemsByGroup(array $sectionData): array
    {
        $mi = $sectionData['mapping_items'] ?? null;

        if (is_array($mi) && $this->isAssocArray($mi)) {
            $out = [];
            foreach ($mi as $groupName => $items) {
                if (!is_string($groupName) || $groupName === '')
                    continue;
                if (!is_array($items))
                    continue;
                $out[$groupName] = array_values(array_filter(array_map(fn($x) => is_array($x) ? $x : [], $items)));
            }
            return $out;
        }

        if (is_array($mi)) {
            return ['items' => array_values(array_filter(array_map(fn($x) => is_array($x) ? $x : [], $mi)))];
        }

        $fieldArrays = [];
        foreach ($sectionData as $key => $value) {
            if ($key === 'data' || $key === 'mapping_items')
                continue;
            if (is_array($value))
                $fieldArrays[$key] = $value;
        }
        if (empty($fieldArrays))
            return [];

        $maxLen = max(array_map('count', $fieldArrays));
        $items = [];
        for ($i = 0; $i < $maxLen; $i++) {
            $item = [];
            foreach ($fieldArrays as $fieldName => $arr) {
                $item[$fieldName] = $arr[$i] ?? '';
            }
            $items[] = $item;
        }
        return ['items' => $items];
    }

    private function renderRepeatableGroups(string $html, array $mappingItemsByGroup): string
    {
        $html = preg_replace_callback(
            '/<!--\s*START\s+REPEATABLE\s+([a-zA-Z0-9_]+)\s*-->(.*?)<!--\s*END\s+REPEATABLE\s+\1\s*-->/s',
            function ($matches) use ($mappingItemsByGroup) {
                $group = $matches[1];
                $repeatableBlock = $matches[2];
                $items = $mappingItemsByGroup[$group] ?? [];
                if (empty($items))
                    return '';

                $allRepeated = '';
                foreach ($items as $item) {
                    $item = is_array($item) ? $item : [];
                    $allRepeated .= $this->applyItemRepeatableRendering($repeatableBlock, $item);
                }
                return $allRepeated;
            },
            $html
        );

        if (preg_match('/<!--\s*START\s+REPEATABLE\s+ITEM\s*-->/i', $html)) {
            $fallbackGroup = array_key_exists('items', $mappingItemsByGroup)
                ? 'items'
                : (array_key_first($mappingItemsByGroup) ?: 'items');
            $items = $mappingItemsByGroup[$fallbackGroup] ?? [];
            if (!empty($items) && preg_match('/<!-- START REPEATABLE ITEM -->(.*?)<!-- END REPEATABLE ITEM -->/s', $html, $matches)) {
                $repeatableBlock = $matches[1];
                $allRepeatedBlocks = '';
                foreach ($items as $item) {
                    $item = is_array($item) ? $item : [];
                    $allRepeatedBlocks .= $this->applyItemRepeatableRendering($repeatableBlock, $item);
                }
                $html = preg_replace(
                    '/<!-- START REPEATABLE ITEM -->.*?<!-- END REPEATABLE ITEM -->/s',
                    $allRepeatedBlocks,
                    $html
                );
            }
        }

        return $html;
    }

    private function applyItemRepeatableRendering(string $itemBlock, array $item): string
    {
        foreach ($item as $key => $value) {
            if ($this->mappingValueIsNestedRepeatableList($value)) {
                continue;
            }
            $repl = is_array($value) ? json_encode($value) : (string) $value;
            $itemBlock = str_replace("{item.$key}", $repl, $itemBlock);
        }
        $nested = $this->nestedMappingFromItem($item);
        if ($nested !== []) {
            $itemBlock = $this->renderRepeatableGroups($itemBlock, $nested);
        }

        return $itemBlock;
    }

    private function mappingValueIsNestedRepeatableList(mixed $value): bool
    {
        if (!is_array($value) || $value === []) {
            return false;
        }
        if (!array_is_list($value)) {
            return false;
        }
        $first = $value[0] ?? null;

        return is_array($first) && $this->isAssocArray($first);
    }

    /** @return array<string, list<array<string, mixed>>> */
    private function nestedMappingFromItem(array $item): array
    {
        $out = [];
        foreach ($item as $key => $value) {
            if (!$this->mappingValueIsNestedRepeatableList($value)) {
                continue;
            }
            $out[$key] = array_values(array_filter(array_map(
                fn($x) => is_array($x) ? $x : [],
                $value
            )));
        }

        return $out;
    }

    private function isAssocArray(array $arr): bool
    {
        if ($arr === [])
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
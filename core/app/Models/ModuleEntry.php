<?php

namespace App\Models;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleEntry extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'module_id',
        'slug',
        'data',
        'section_data',
        'sort_order',
        'is_published',
        'mapped_to_homepage',
    ];

    protected $casts = [
        'data' => 'array',
        'section_data' => 'array',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'mapped_to_homepage' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'module_entry_page', 'module_entry_id', 'page_id')->withTimestamps();
    }

    public function subPages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'module_entry_sub_pages', 'module_entry_id', 'page_id')
            ->withTimestamps();
    }

    public function relatedEntries(): BelongsToMany
    {
        return $this->belongsToMany(ModuleEntry::class, 'module_entry_mapping', 'module_entry_id', 'related_module_entry_id')->where('is_published', 1)->withTimestamps();
    }


    public function scopeForModule($query, int $id, string $sortField = 'date', string $direction = 'desc', bool $isJson = true)
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query->whereHas('module', fn($q) => $q->where('id', $id))
            ->where('is_published', 1);

        if ($isJson) {
            $query->orderByRaw("
            CAST(
                JSON_UNQUOTE(JSON_EXTRACT(data, '$.{$sortField}'))
                AS UNSIGNED
            ) {$direction}
        ");
        } else {
            $query->orderBy($sortField, $direction);
        }

        return $query;
    }

    public function toCleanData(): array
    {
        $data = $this->data ?? [];
        unset($data['sections']);

        if ($this->slug !== null) {
            $data['slug'] = $this->slug;
        }

        $data['id'] = $this->id;

        return $data;
    }

    public static function getData(int $id, int $perPage = 10, string $sortField = 'title', string $direction = 'asc')
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $result = self::whereHas('module', fn($q) => $q->where('id', $id))
            ->where('is_published', 1)
            ->paginate($perPage);

        $sorted = $result->getCollection()
            ->map(function ($entry) {
                $data = $entry->data ?? [];

                unset($data['sections']);

                if ($entry->slug != null) {
                    $data['slug'] = $entry->slug;
                }

                return $data;
            })
            ->sortBy(function ($item) use ($sortField) {
                return $item[$sortField] ?? null;
            }, SORT_REGULAR, $direction === 'desc')
            ->values();

        $result->setCollection($sorted);

        return $result;
    }

    public static function getDetailData(int $id)
    {
        $entry = self::with(['relatedEntries.module'])
            ->where('id', $id)
            ->where('is_published', 1)
            ->first();

        if (!$entry) {
            return [];
        }

        $storedSections = is_array(($entry->data ?? [])['sections'] ?? null) ? $entry->data['sections'] : [];

        $sectionsData = [];
        foreach ($storedSections as $item) {
            $item = is_array($item) ? $item : [];
            if (!empty($item['is_hidden'])) {
                continue;
            }

            $sectionId = $item['section_id'] ?? null;
            if (!$sectionId) {
                continue;
            }

            $section = PageSection::query()->where('id', $sectionId)->first();
            if (!$section) {
                continue;
            }

            $html = self::renderSectionHtmlTemplate(
                (string) ($section->html_template ?? ''),
                [
                    'data' => is_array($item['data'] ?? null) ? $item['data'] : [],
                    'mapping_items' => is_array($item['mapping_items'] ?? null) ? $item['mapping_items'] : [],
                ]
            );

            $sectionsData[$section->identifier] = $html;
        }

        $data = $entry->data ?? [];
        $modular = [];
        unset($data['sections']);

        if ($entry->relatedEntries->isNotEmpty()) {
            foreach ($entry->relatedEntries as $related) {
                $moduleSlug = $related->module->slug ?? null;
                if ($moduleSlug) {
                    $relatedData = $related->data ?? [];
                    unset($relatedData['sections']);
                    $modular[$moduleSlug][] = $relatedData;
                }
            }
        }

        return [
            'detail' => $data,
            'cms' => $sectionsData,
            'modular' => $modular,
        ];
    }

    public static function getMappedDataWithPagination(int $departmentEntryId, int $moduleId, array $relatedModuleIds = [], int $perPage = 10, string $sortField = 'sort_order', string $direction = 'asc'): ?LengthAwarePaginator 
    {
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $entry = self::where('id', $departmentEntryId)
            ->whereHas('module', fn($q) => $q->where('id', $moduleId))
            ->where('is_published', 1)
            ->first();

        if (!$entry) {
            return null;
        }

        $query = $entry->relatedEntries()->where('is_published', 1);

        if (!empty($relatedModuleIds)) {
            $query->whereHas('module', fn($q) => $q->whereIn('id', $relatedModuleIds));
        }

        return $query->orderBy($sortField, $direction)->paginate($perPage)->through(fn($related) => $related->toCleanData());
    }

    private static function renderSectionHtmlTemplate(string $sectionHtml, array $sectionData): string
    {
        $data = is_array($sectionData['data'] ?? null) ? $sectionData['data'] : [];
        foreach ($data as $key => $value) {
            $sectionHtml = str_replace("{{$key}}", (string) $value, $sectionHtml);
        }

        $mappingItemsByGroup = self::normalizeMappingItemsByGroup($sectionData);
        if (!empty($mappingItemsByGroup)) {
            $sectionHtml = self::renderRepeatableGroups($sectionHtml, $mappingItemsByGroup);
        }

        return $sectionHtml;
    }

    private static function normalizeMappingItemsByGroup(array $sectionData): array
    {
        $mi = $sectionData['mapping_items'] ?? null;

        if (is_array($mi) && self::isAssocArray($mi)) {
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

        return [];
    }

    private static function renderRepeatableGroups(string $html, array $mappingItemsByGroup): string
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
                    $allRepeated .= self::applyItemRepeatableRendering($repeatableBlock, $item);
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
                    $allRepeatedBlocks .= self::applyItemRepeatableRendering($repeatableBlock, $item);
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

    private static function applyItemRepeatableRendering(string $itemBlock, array $item): string
    {
        foreach ($item as $key => $value) {
            if (self::mappingValueIsNestedRepeatableList($value)) {
                continue;
            }
            $repl = is_array($value) ? json_encode($value) : (string) $value;
            $itemBlock = str_replace("{item.$key}", $repl, $itemBlock);
        }
        $nested = self::nestedMappingFromItem($item);
        if ($nested !== []) {
            $itemBlock = self::renderRepeatableGroups($itemBlock, $nested);
        }

        return $itemBlock;
    }

    private static function mappingValueIsNestedRepeatableList(mixed $value): bool
    {
        if (!is_array($value) || $value === []) {
            return false;
        }
        if (!array_is_list($value)) {
            return false;
        }
        $first = $value[0] ?? null;

        return is_array($first) && self::isAssocArray($first);
    }

    /** @return array<string, list<array<string, mixed>>> */
    private static function nestedMappingFromItem(array $item): array
    {
        $out = [];
        foreach ($item as $key => $value) {
            if (!self::mappingValueIsNestedRepeatableList($value)) {
                continue;
            }
            $out[$key] = array_values(array_filter(array_map(
                fn($x) => is_array($x) ? $x : [],
                $value
            )));
        }

        return $out;
    }

    private static function isAssocArray(array $arr): bool
    {
        if ($arr === [])
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}


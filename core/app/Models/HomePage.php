<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $fillable = [
        'title',
        'sections',
        'is_active',
    ];

    protected $casts = [
        'sections' => 'array',
        'is_active' => 'boolean',
    ];

    public static function singleton(): self
    {
        $homePages = static::query()->orderBy('id')->get();

        return $homePages->first(function (self $homePage) {
            return is_array($homePage->sections) && count($homePage->sections) > 0;
        }) ?: $homePages->first() ?: static::query()->create([
                        'title' => 'Home Page',
                        'sections' => [],
                        'is_active' => true,
                    ]);
    }

    public function publicPayload(): array
    {
        $payload = [
            'title' => $this->title,
        ];

        foreach ($this->sections ?? [] as $section) {
            if (!($section['is_active'] ?? false)) {
                continue;
            }

            $sectionData = [];

            foreach ($section['fields'] ?? [] as $field) {
                $type = $field['type'] ?? 'text';
                $value = $field['value'] ?? null;

                if (in_array($type, ['image', 'file'], true)) {
                    $sectionData[$field['name']] = $this->resolveFileUrl($value);
                } elseif ($type === 'repeater') {
                    $rows = $value ?? [];
                    $childFields = $field['fields'] ?? [];
                    $mappedRows = [];
                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            if (!is_array($row)) continue;
                            $mappedRow = [];
                            foreach ($childFields as $child) {
                                $cName = $child['name'] ?? '';
                                $cValue = $row[$cName] ?? null;
                                if (in_array($child['type'] ?? '', ['image', 'file'], true)) {
                                    $mappedRow[$cName] = $this->resolveFileUrl($cValue);
                                } else {
                                    $mappedRow[$cName] = $cValue;
                                }
                            }
                            $mappedRows[] = $mappedRow;
                        }
                    }
                    $sectionData[$field['name']] = $mappedRows;
                } else {
                    $sectionData[$field['name']] = $value;
                }
            }

            $key = \Illuminate\Support\Str::snake($section['title']);

            $payload[$key] = $sectionData;
        }

        return $payload;
    }

    private function resolveFileUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, 'assets/')) {
            return asset($value);
        }

        return asset('assets/img/home-page/' . $value);
    }

    private function publicFieldPayload(array $field): array
    {
        $payload = [
            'label' => $field['label'] ?? '',
            'value' => $field['value'] ?? null,
        ];

        if (($field['type'] ?? null) === 'repeater') {
            $children = collect($field['fields'] ?? [])->values();
            $payload['value'] = collect($field['value'] ?? [])
                ->filter(fn($row) => is_array($row))
                ->map(function ($row) use ($children) {
                    return $children
                        ->map(fn($child) => [
                            'label' => $child['label'] ?? '',
                            'value' => $row[$child['name'] ?? ''] ?? null,
                        ])
                        ->all();
                })
                ->values()
                ->all();
        }

        return $payload;
    }
}

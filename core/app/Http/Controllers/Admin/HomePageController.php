<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class HomePageController extends Controller
{
    private const FIELD_TYPES = [
        'text',
        'textarea',
        'code',
        'number',
        'email',
        'url',
        'select',
        'checkbox',
        'radio',
        'file',
        'image',
        'date',
        'color',
        'repeater',
    ];

    public function edit()
    {
        $homePage = HomePage::singleton();

        return Inertia::render('HomePage/Edit', [
            'homePage' => [
                'id' => $homePage->id,
                'title' => $homePage->title,
                'is_active' => (bool) $homePage->is_active,
                'sections' => is_array($homePage->sections) ? $homePage->sections : [],
            ],
            'fieldTypes' => self::FIELD_TYPES,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sections_json' => 'required|string',
            'field_files' => 'sometimes|array',
            'field_files.*.*' => 'file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,avi,mov,ico,pdf,doc,docx|max:20000',
            'repeater_files' => 'sometimes|array',
            'repeater_files.*.*.*.*' => 'file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,avi,mov,ico,pdf,doc,docx|max:20000',
        ]);

        $sections = json_decode($validated['sections_json'], true);

        if (! is_array($sections)) {
            throw ValidationException::withMessages([
                'sections_json' => 'Sections must be valid JSON.',
            ]);
        }

        $sections = $this->mergeUploadedFiles($request, $sections);
        $sections = $this->normalizeSections($sections);

        HomePage::singleton()->update([
            'title' => $validated['title'],
            'is_active' => $request->boolean('is_active', true),
            'sections' => $sections,
        ]);

        return redirect()
            ->route('home-page.edit')
            ->with('success', 'Home page saved successfully!');
    }

    private function normalizeSections(array $sections): array
    {
        return collect($sections)
            ->values()
            ->filter(function ($section) {
                if (! is_array($section)) {
                    return false;
                }

                $title = trim((string) ($section['title'] ?? ''));
                $fields = collect($section['fields'] ?? [])
                    ->filter(fn ($field) => $this->fieldHasContent($field));

                return $title !== '' || $fields->isNotEmpty();
            })
            ->map(function ($section, int $sectionIndex) {
                if (! is_array($section)) {
                    $section = [];
                }

                $fields = collect($section['fields'] ?? [])
                    ->values()
                    ->filter(fn ($field) => $this->fieldHasContent($field))
                    ->map(function ($field, int $fieldIndex) use ($sectionIndex) {
                        if (! is_array($field)) {
                            $field = [];
                        }

                        $type = in_array($field['type'] ?? 'text', self::FIELD_TYPES, true)
                            ? $field['type']
                            : 'text';

                        $name = (string) ($field['name'] ?? '');
                        if ($name === '' || ! preg_match('/^[a-z][a-z0-9_]*$/', $name)) {
                            $name = 'field_' . ($sectionIndex + 1) . '_' . ($fieldIndex + 1);
                        }

                        $options = $field['options'] ?? [];
                        if (is_string($options)) {
                            $options = array_values(array_filter(array_map('trim', explode(',', $options))));
                        }

                        $children = $type === 'repeater'
                            ? $this->normalizeRepeaterFields($field['fields'] ?? [], $sectionIndex, $fieldIndex)
                            : [];

                        $value = $type === 'repeater'
                            ? $this->normalizeRepeaterValue($field['value'] ?? [], $children)
                            : $this->normalizeFieldValue($type, $field['value'] ?? null);

                        return [
                            'name' => $name,
                            'label' => (string) ($field['label'] ?? 'Field ' . ($fieldIndex + 1)),
                            'type' => $type,
                            'required' => (bool) ($field['required'] ?? false),
                            'placeholder' => (string) ($field['placeholder'] ?? ''),
                            'options' => is_array($options) ? array_values($options) : [],
                            'fields' => $children,
                            'value' => $value,
                        ];
                    })
                    ->filter(fn ($field) => trim($field['label']) !== '')
                    ->values()
                    ->all();

                return [
                    'key' => (string) ($section['key'] ?? uniqid('section_', true)),
                    'title' => (string) ($section['title'] ?? 'Section ' . ($sectionIndex + 1)),
                    'order' => (int) ($section['order'] ?? $sectionIndex),
                    'is_active' => (bool) ($section['is_active'] ?? true),
                    'fields' => $fields,
                ];
            })
            ->filter(fn ($section) => count($section['fields'] ?? []) > 0)
            ->values()
            ->map(fn ($section, int $index) => [
                ...$section,
                'order' => $index,
            ])
            ->all();
    }

    private function fieldHasContent(mixed $field): bool
    {
        if (! is_array($field)) {
            return false;
        }

        return trim((string) ($field['label'] ?? '')) !== ''
            || trim((string) ($field['name'] ?? '')) !== ''
            || $this->fieldValueHasContent($field['value'] ?? null);
    }

    private function fieldValueHasContent(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_array($value)) {
            return ! empty($value);
        }

        return trim((string) ($value ?? '')) !== '';
    }

    private function normalizeFieldValue(string $type, mixed $value): mixed
    {
        return match ($type) {
            'checkbox' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => $value === null || $value === '' ? '' : $value,
            default => is_array($value) ? '' : (string) ($value ?? ''),
        };
    }

    private function normalizeRepeaterFields(mixed $fields, int $sectionIndex, int $repeaterIndex): array
    {
        return collect(is_array($fields) ? $fields : [])
            ->values()
            ->filter(fn ($field) => $this->fieldHasContent($field))
            ->map(function ($field, int $fieldIndex) use ($sectionIndex, $repeaterIndex) {
                $field = is_array($field) ? $field : [];
                $type = in_array($field['type'] ?? 'text', self::FIELD_TYPES, true)
                    ? $field['type']
                    : 'text';

                if ($type === 'repeater') {
                    $type = 'text';
                }

                $name = (string) ($field['name'] ?? '');
                if ($name === '' || ! preg_match('/^[a-z][a-z0-9_]*$/', $name)) {
                    $name = 'field_' . ($sectionIndex + 1) . '_' . ($repeaterIndex + 1) . '_' . ($fieldIndex + 1);
                }

                $options = $field['options'] ?? [];
                if (is_string($options)) {
                    $options = array_values(array_filter(array_map('trim', explode(',', $options))));
                }

                return [
                    'name' => $name,
                    'label' => (string) ($field['label'] ?? 'Field ' . ($fieldIndex + 1)),
                    'type' => $type,
                    'required' => (bool) ($field['required'] ?? false),
                    'placeholder' => (string) ($field['placeholder'] ?? ''),
                    'options' => is_array($options) ? array_values($options) : [],
                    'value' => '',
                ];
            })
            ->filter(fn ($field) => trim($field['label']) !== '')
            ->values()
            ->all();
    }

    private function normalizeRepeaterValue(mixed $value, array $fields): array
    {
        return collect(is_array($value) ? $value : [])
            ->filter(fn ($row) => is_array($row))
            ->map(function ($row) use ($fields) {
                $out = [];

                foreach ($fields as $field) {
                    $name = $field['name'] ?? null;
                    if (! $name) {
                        continue;
                    }

                    $out[$name] = $this->normalizeFieldValue(
                        $field['type'] ?? 'text',
                        $row[$name] ?? null
                    );
                }

                return $out;
            })
            ->filter(fn ($row) => collect($row)->contains(fn ($value) => $this->fieldValueHasContent($value)))
            ->values()
            ->all();
    }

    private function mergeUploadedFiles(Request $request, array $sections): array
    {
        foreach ($sections as $sectionIndex => $section) {
            foreach (($section['fields'] ?? []) as $fieldIndex => $field) {
                if (($field['type'] ?? '') === 'repeater') {
                    $rows = $field['value'] ?? [];
                    if (is_array($rows)) {
                        foreach ($rows as $rowIndex => $row) {
                            foreach (($field['fields'] ?? []) as $childField) {
                                if (in_array($childField['type'] ?? '', ['file', 'image'], true)) {
                                    $cName = $childField['name'];
                                    $file = $request->file("repeater_files.{$sectionIndex}.{$fieldIndex}.{$rowIndex}.{$cName}");
                                    if ($file instanceof UploadedFile) {
                                        $sections[$sectionIndex]['fields'][$fieldIndex]['value'][$rowIndex][$cName] = $this->uploadFile($file);
                                    }
                                }
                            }
                        }
                    }
                    continue;
                }

                if (! in_array($field['type'] ?? '', ['file', 'image'], true)) {
                    continue;
                }

                $file = $request->file("field_files.{$sectionIndex}.{$fieldIndex}");
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $sections[$sectionIndex]['fields'][$fieldIndex]['value'] = $this->uploadFile($file);
            }
        }

        return $sections;
    }

    private function uploadFile(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = 'homepage_' . time() . '_' . uniqid() . '.' . $extension;

        $folder = 'assets/img/home-page/';
        $path = public_path($folder);

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $file->move($path, $filename);

        return asset($folder . $filename);
    }
}

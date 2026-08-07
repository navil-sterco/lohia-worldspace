<?php

use App\Http\Controllers\ModuleEntryController;

test('normalize mapping config preserves nested parent_group metadata', function () {
    $controller = new ModuleEntryController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('normalizeMappingConfigGroups');
    $method->setAccessible(true);

    $config = [
        [
            'group_label' => 'Items',
            'group_name' => 'items',
            'parent_group' => '',
            'fields' => [['name' => 'title']],
        ],
        [
            'group_label' => 'Cells',
            'group_name' => 'cells',
            'parent_group' => 'items',
            'fields' => [['name' => 'image']],
        ],
    ];

    $normalized = $method->invoke($controller, $config);

    expect($normalized)
        ->toHaveCount(2)
        ->and($normalized[1]['parent_group'])->toBe('items')
        ->and($normalized[1]['group_name'])->toBe('cells');
});

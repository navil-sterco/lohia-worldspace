<?php

use App\Models\Page;
use App\Models\SupportInformation;

function headerData()
{
    return Page::whereJsonContains('display_location', 'header')
        ->with('children')
        ->orderBy('display_order', 'ASC')
        ->get()
        ->map(function ($page) {
            return [
                'title' => $page->title,
                'slug' => $page->slug,
                'target_blank' => $page->target_blank,
                'children' => $page->children->map(function ($child) {
                    return [
                        'title' => $child->title,
                        'slug' => $child->slug,
                        'target_blank' => $child->target_blank,
                    ];
                })->values(),
            ];
        });
}

function footerData()
{
    return Page::whereJsonContains('display_location', 'footer')
        ->orderBy('display_order', 'ASC')
        ->get()
        ->map(function ($page) {
            return [
                'title' => $page->title,
                'slug' => $page->slug,
                'target_blank' => $page->target_blank,
            ];
        });
}

function sidebar()
{
    return Page::whereJsonContains('display_location', 'sidebar')
        ->with('children')
        ->orderBy('display_order', 'ASC')
        ->get()
        ->map(function ($page) {
            return [
                'title' => $page->title,
                'slug' => $page->slug,
                'target_blank' => $page->target_blank,
                'children' => $page->children->map(function ($child) {
                    return [
                        'title' => $child->title,
                        'slug' => $child->slug,
                        'target_blank' => $child->target_blank,
                    ];
                })->values(),
            ];
        });
}
function quickLinks()
{
    return Page::whereJsonContains('display_location', 'quick-links')
        ->orderBy('display_order', 'ASC')
        ->get()
        ->map(function ($page) {
            return [
                'title' => $page->title,
                'slug' => $page->slug,
                'target_blank' => $page->target_blank,
            ];
        });
}

function socialIcons()
{
    return SupportInformation::where('key', 'social')->get()->map(function ($item) {
        return [
            'key' => $item->key,
            'value' => $item->value,
            'image' => $item->file_path ? asset($item->file_path) : null,
        ];
    });
}


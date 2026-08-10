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

function insightMenu(string $slug): array
{
    $page = Page::published()
        ->where('page_type', 'cms')
        ->where('slug', $slug)
        ->first();

    if (!$page) {
        return ['parent' => null, 'items' => collect()];
    }

    if ($page->parent_page_id) {
        $parent = Page::find($page->parent_page_id);

        $items = Page::published()
            ->where('page_type', 'cms')
            ->where('parent_page_id', $page->parent_page_id)
            ->orderBy('display_order')
            ->get(['title', 'slug']);
    } else {
        $parent = $page;

        $items = Page::published()
            ->where('page_type', 'cms')
            ->where('parent_page_id', $page->id)
            ->orderBy('display_order')
            ->get(['title', 'slug']);
    }

    return [
        'parent' => $parent,
        'items' => $items,
    ];
}


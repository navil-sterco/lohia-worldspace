<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Http\Controllers\Controller;

class CMSController extends Controller
{
    public function index($slug)
    {
        $page = Page::published()->where('page_type', 'cms')->bySlug($slug)->firstOrFail();

        if ($page->parent_page_id) {
            $relatedPages = Page::published()
                ->where('page_type', 'cms')
                ->where('parent_page_id', $page->parent_page_id)
                ->where('id', '!=', $page->id)
                ->orderBy('display_order')
                ->get(['title', 'slug']);
        } else {
            $relatedPages = Page::published()
                ->where('page_type', 'cms')
                ->where('parent_page_id', $page->id)
                ->where('id', '!=', $page->id)
                ->orderBy('display_order')
                ->get(['title', 'slug']);
        }

        if ($page->sections->isEmpty()) {
            return view('coming-soon.coming-soon');
        }

        return view('cms.show', [
            'page' => $page,
            'renderedHtml' => $page->getRenderedHtml(),
            'relatedPages' => $relatedPages,
        ]);
    }
}

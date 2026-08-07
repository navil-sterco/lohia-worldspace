<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Http\Controllers\Controller;

class ModularPageController extends Controller
{
    public function index($slug)
    {
        $page = Page::published()
            ->where('page_type', 'modular')
            ->bySlug($slug)
            ->firstOrFail();

        $viewData = $page->getModularPageData();

        return view('modular.' . $slug, array_merge([
            'page' => $page,
        ], $viewData));
    }
}

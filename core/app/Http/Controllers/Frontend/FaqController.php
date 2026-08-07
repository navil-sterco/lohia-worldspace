<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ModuleEntry;
use App\Models\Page;

class FaqController extends Controller
{
    public function index()
    {
        $page = Page::published()->bySlug('faq')->firstOrFail();
        $viewData = $page->getModularPageData();
        $faqs = ModuleEntry::forModule(5, 'display_order', 'asc')->get()->map(fn($e) => $e->toCleanData());

        return view('dynamic.faq', [
            'page' => $page,
            'section' => $viewData,
            'faqs' => $faqs,
        ]);
    }
}

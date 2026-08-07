<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Page;
use App\Models\ModuleEntry;
use App\Http\Controllers\Controller;

class PeopleController extends Controller
{
    public function index()
    {
        try {

            $page = Page::published()->bySlug('people')->firstOrFail();
            $viewData = $page->getModularPageData();

            $openings = ModuleEntry::forModule(4, 'display_order', 'asc')->paginate(10)->through(fn($e) => $e->toCleanData());

            return view('dynamic.people', [
                'page' => $page,
                'section' => $viewData,
                'openings' => $openings,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

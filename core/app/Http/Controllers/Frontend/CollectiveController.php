<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModuleEntry;
use App\Models\Page;

class CollectiveController extends Controller
{
    public function index(Request $request)
    {
        try {

            $page = Page::published()->bySlug('collective')->firstOrFail();
            $viewData = $page->getModularPageData();

            $query = ModuleEntry::forModule(9, 'display_order', 'asc');
            $collectives = $query->paginate(10)->withQueryString()->through(fn($e) => $e->toCleanData());

            return view('dynamic.collective', [
                'collectives' => $collectives,
                'section' => $viewData,


            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($slug)
    {
        $collectiveDetail = ModuleEntry::where('slug', $slug)->where('module_id', 9)->firstOrFail();
        $collectiveDetail = $collectiveDetail->getDetailData($collectiveDetail->id);

        return view('dynamic.collective-detail', [
            'collectiveDetail' => $collectiveDetail
        ]);
    }
}

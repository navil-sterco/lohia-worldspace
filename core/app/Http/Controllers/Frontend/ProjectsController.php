<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ModuleEntry;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{

    public function index(Request $request)
    {
        try {

            $page = Page::published()->bySlug('projects')->firstOrFail();

            $viewData = $page->getModularPageData();

            $query = ModuleEntry::forModule(3, 'display_order', 'asc');

            $query->when(
                $request->filled('type'),
                fn($q) =>
                $q->where('data->type', $request->input('type'))
            );

            $query->when(
                $request->filled('location'),
                fn($q) =>
                $q->where('data->location', $request->input('location'))
            );

            $query->when(
                $request->filled('status'),
                fn($q) =>
                $q->where('data->status', $request->input('status'))
            );

            $projects = $query->paginate(10)
                ->withQueryString()
                ->through(fn($e) => $e->toCleanData());

            $filterOptions = [
                'types' => ModuleEntry::forModule(3)
                    ->select(DB::raw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(data, '$.type')) as type"))
                    ->pluck('type')
                    ->filter()
                    ->values(),

                'locations' => ModuleEntry::forModule(3)
                    ->select(DB::raw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(data, '$.location')) as location"))
                    ->pluck('location')
                    ->filter()
                    ->values(),

                'statuses' => ModuleEntry::forModule(3)
                    ->select(DB::raw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(data, '$.status')) as status"))
                    ->pluck('status')
                    ->filter()
                    ->values(),
            ];

            return view('dynamic.project', [
                'projects' => $projects,
                'section' => $viewData,
                'filterOptions' => $filterOptions,
                'activeFilters' => $request->only(['type', 'location', 'status']),
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
        $projectDetail = ModuleEntry::where('slug', $slug)
            ->where('module_id', 3)
            ->firstOrFail();

        $projectDetail = $projectDetail->getDetailData($projectDetail->id);


        return view('dynamic.project-detail', [
            'projectDetail' => $projectDetail
        ]);
    }
}

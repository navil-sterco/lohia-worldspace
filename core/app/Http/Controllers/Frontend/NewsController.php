<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ModuleEntry;

class NewsController extends Controller
{
    public function index()
    {
        $news = ModuleEntry::forModule(1, 'display_order', 'asc')
            ->paginate(10)
            ->through(fn($e) => $e->toCleanData());

        $firstNews = $news->first();

        $news->setCollection(
            $news->getCollection()->slice(1)->values()
        );

        return view('dynamic.news', [
            'firstNews' => $firstNews,
            'news' => $news,
        ]);
    }

    public function detail($slug)
    {
        $newsDetail = ModuleEntry::where('slug', $slug)->where('module_id', 1)->firstOrFail();
        $newsDetail = $newsDetail->toCleanData();

        $relatedNews = ModuleEntry::where('module_id', 1)
            ->where('slug', '!=', $slug)
            ->take(2)
            ->get()
            ->map(fn($item) => $item->toCleanData());

        return view('dynamic.news-detail', [
            'newsDetail' => $newsDetail,
            'relatedNews' => $relatedNews,
        ]);
    }
}

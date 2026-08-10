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
}

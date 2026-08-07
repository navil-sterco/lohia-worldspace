<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ModuleEntry;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        $blog = ModuleEntry::forModule(6, 'display_order', 'asc')
            ->paginate(10)
            ->through(fn($e) => $e->toCleanData());

        $firstBlog = $blog->first();

        $blog->setCollection(
            $blog->getCollection()->slice(1)->values()
        );

        return view('dynamic.blog', [
            'firstBlog' => $firstBlog,
            'blog' => $blog,
        ]);
    }

    public function detail($slug)
    {
        $blogDetail = ModuleEntry::where('slug', $slug)->where('module_id', 6)->firstOrFail();
        $blogDetail = $blogDetail->getDetailData($blogDetail->id);


        $relatedBlogs = ModuleEntry::where('module_id', 6)
            ->where('slug', '!=', $slug)
            ->take(2)
            ->get()
            ->map(fn($item) => $item->toCleanData());

        return view('dynamic.blog-detail', [
            'blogDetail' => $blogDetail,
            'relatedBlogs' => $relatedBlogs,
        ]);
    }
}

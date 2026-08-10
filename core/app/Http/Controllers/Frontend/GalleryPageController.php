<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ModuleEntry;
use App\Http\Controllers\Controller;

class GalleryPageController extends Controller
{
    public function index()
    {
        $gallery = ModuleEntry::forModule(8, 'display_order', 'asc')
            ->paginate(10)
            ->through(fn($e) => $e->toCleanData());

        return view('dynamic.gallery', [
            'gallery' => $gallery,
        ]);
    }
}

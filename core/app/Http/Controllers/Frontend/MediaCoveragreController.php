<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ModuleEntry;
use Illuminate\Http\Request;

class MediaCoveragreController extends Controller
{
    public function index()
    {
        $media = ModuleEntry::forModule(10, 'display_order', 'asc')->paginate(10)->through(fn($e) => $e->toCleanData());
        return view('dynamic.media-coverage', compact('media'));
    }
}

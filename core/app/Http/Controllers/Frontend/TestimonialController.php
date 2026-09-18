<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ModuleEntry;
use App\Http\Controllers\Controller;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = ModuleEntry::forModule(2, 'display_order', 'asc')
            ->paginate(12)
            ->through(fn($e) => $e->toCleanData());


        return view('dynamic.testimonials', [
            'testimonials' => $testimonials,
        ]);
    }
}

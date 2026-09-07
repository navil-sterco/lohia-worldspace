<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaCoveragreController extends Controller
{
    public function index()
    {
        return view('dynamic.media-coverage');
    }
}

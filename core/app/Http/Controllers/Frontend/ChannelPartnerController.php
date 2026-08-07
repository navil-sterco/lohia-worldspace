<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class ChannelPartnerController extends Controller
{
    public function index()
    {
        $page = Page::published()->bySlug('channel-partners')->firstOrFail();
        $viewData = $page->getModularPageData();
        return view('dynamic.channel-partners', [
            'page' => $page,
            'section' => $viewData,
        ]);
    }

}

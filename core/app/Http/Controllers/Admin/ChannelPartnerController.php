<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChannelPartner;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChannelPartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-channel-partner')->only('index');
        $this->middleware('permission:delete-channel-partner')->only('destroy');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $partners = ChannelPartner::filter(['search' => $search])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ChannelPartner/Index', [
            'submissions' => $partners,
            'searchTerm' => $search ?? '',
        ]);
    }

    public function destroy(ChannelPartner $channelPartner)
    {
        $channelPartner->delete();

        return back()->with('success', 'Channel partner application deleted successfully!');
    }
}

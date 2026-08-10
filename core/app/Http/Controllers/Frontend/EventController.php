<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ModuleEntry;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        $events = ModuleEntry::forModule(7, 'display_order', 'asc')
            ->paginate(10)
            ->through(fn($e) => $e->toCleanData());
    
        $firstEvents = $events->first();

        $events->setCollection(
            $events->getCollection()->slice(1)->values()
        );

        return view('dynamic.events', [
            'firstEvents' => $firstEvents,
            'events' => $events,
        ]);
    }

    public function detail($slug)
    {
        $eventDetail = ModuleEntry::where('slug', $slug)->where('module_id', 7)->firstOrFail();
        $eventDetail = $eventDetail->toCleanData();

        $relatedEvents = ModuleEntry::where('module_id', 7)
            ->where('slug', '!=', $slug)
            ->take(2)
            ->get()
            ->map(fn($item) => $item->toCleanData());

        return view('dynamic.events-detail', [
            'eventDetail' => $eventDetail,
            'relatedEvents' => $relatedEvents,
        ]);
    }
}

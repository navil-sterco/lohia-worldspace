<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tab;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TabController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-tab')->only(['index', 'show']);
        $this->middleware('permission:create-tab')->only(['create', 'store']);
        $this->middleware('permission:edit-tab')->only(['edit', 'update']);
        $this->middleware('permission:delete-tab')->only(['destroy']);
    }

    public function index()
    {
        $tabs = Tab::withCount('pages')->orderBy('display_order')->get();
        return Inertia::render('Tab/Index', [
            'tabs' => $tabs
        ]);
    }

    public function create()
    {
        return Inertia::render('Tab/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'heading' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'display_order' => 'integer',
        ]);

        Tab::create($validated);

        return redirect()->route('tabs.index')->with('success', 'Tab created successfully!');
    }

    public function edit(Tab $tab)
    {
        return Inertia::render('Tab/Edit', [
            'tab' => $tab
        ]);
    }

    public function update(Request $request, Tab $tab)
    {
        $validated = $request->validate([
            'heading' => 'required|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'display_order' => 'integer',
        ]);

        $tab->update($validated);

        return redirect()->route('tabs.index')->with('success', 'Tab updated successfully!');
    }

    public function destroy(Tab $tab)
    {
        $tab->delete();
        return redirect()->route('tabs.index')->with('success', 'Tab deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SupportInformation;
use App\Http\Controllers\Controller;


class SupportInformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-support')->only(['index', 'show']);
        $this->middleware('permission:create-support')->only(['create', 'store']);
        $this->middleware('permission:edit-support')->only(['edit', 'update']);
        $this->middleware('permission:delete-support')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $search = $request->input('search');

        $supportInfo = SupportInformation::filter(['search' => $search])
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($item) {
                return [
                    'id' => $item->id,
                    'key' => $item->key,
                    'value' => $item->value,
                    'file_path' => $item->file_path ? asset($item->file_path) : null,
                    'created_at' => $item->created_at->format('M d, Y'),
                ];
            });

        return Inertia::render('SupportInformation/Index', [
            'supportInfo' => $supportInfo,
            'searchTerm' => $search ?? '',
        ]);
    }

    public function create()
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/support-information')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.support' => $previousUrl]);
            }
        }

        return Inertia::render('SupportInformation/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $name = time() . '_' . Str::random(20) . '.' . $extension;
            $filePath = 'assets/img/support-info/' . $name;

            try {
                $file->move(public_path('assets/img/support-info'), $name);
                $validated['file_path'] = $filePath;
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
            }
        }

        SupportInformation::create($validated);

        $returnUrl = session()->pull('return_url.support', route('support-information.index'));
        return redirect()->to($returnUrl)->with('success', 'Support information added successfully!');
    }

    public function edit(SupportInformation $supportInformation)
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/support-information')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.support' => $previousUrl]);
            }
        }

        return Inertia::render('SupportInformation/Edit', [
            'supportInformation' => [
                'id' => $supportInformation->id,
                'key' => $supportInformation->key,
                'value' => $supportInformation->value,
                'file_path' => $supportInformation->file_path ? asset($supportInformation->file_path) : null,
            ]
        ]);
    }

    public function update(Request $request, SupportInformation $supportInformation)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('file')) {
            if ($supportInformation->file_path && file_exists(public_path($supportInformation->file_path))) {
                unlink(public_path($supportInformation->file_path));
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $name = time() . '_' . Str::random(20) . '.' . $extension;
            $filePath = 'assets/img/support-info/' . $name;

            try {
                $file->move(public_path('assets/img/support-info'), $name);
                $validated['file_path'] = $filePath;
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
            }
        }

        $supportInformation->update($validated);

        $returnUrl = session()->pull('return_url.support', route('support-information.index'));
        return redirect()->to($returnUrl)->with('success', 'Support information updated successfully!');
    }

    public function destroy(SupportInformation $supportInformation)
    {
        try {
            if ($supportInformation->file_path && file_exists(public_path($supportInformation->file_path))) {
                unlink(public_path($supportInformation->file_path));
            }
            $supportInformation->delete();

            return back()->with('success', 'Support information deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
}

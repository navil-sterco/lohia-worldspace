<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SeoSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-seo')->only(['index', 'show']);
        $this->middleware('permission:create-seo')->only(['create', 'store']);
        $this->middleware('permission:edit-seo')->only(['edit', 'update']);
        $this->middleware('permission:delete-seo')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $seo = SeoSetting::filter(['search' => $search])->orderBy('id', 'DESC')->paginate(10)->withQueryString()->through(function ($item) {
            return [
                'id' => $item->id,
                'url' => $item->url,
                'meta_title' => $item->meta_title,
                'meta_description' => $item->meta_description,
                'keywords' => $item->keywords ?? [],
                'search_terms' => $item->search_terms ?? [],
                'canonical_url' => $item->canonical_url,
                'og_title' => $item->og_title,
                'og_description' => $item->og_description,
                'og_image' => $item->og_image ? asset($item->og_image) : asset('assets/img/placeholder.png'),
                'og_type' => $item->og_type,
                'og_url' => $item->og_url,
                'created_at' => $item->created_at->format('M d, Y'),
                'updated_at' => $item->updated_at->format('M d, Y'),
            ];
        });

        return Inertia::render('SeoSetting/Index', [
            'searchTerm' => $search ?? '',
            'seo' => $seo,
        ]);
    }

    public function create()
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/seo')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.seo' => $previousUrl]);
            }
        }

        return Inertia::render('SeoSetting/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'required|string|max:255',
            'url' => [
                'required',
                'string',
                'max:255',
                'unique:seo_settings,url',
            ],
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|array',
            'keywords.*' => 'string|max:50',
            'search_terms' => 'nullable|array',
            'search_terms.*' => 'string|max:50',
            'canonical_url' => 'nullable|url',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'og_type' => 'nullable|string',
            'og_url' => 'nullable|url',
        ]);

        $validated['url'] = ltrim($validated['url'], '/');

        if ($request->hasFile('og_image')) {
            $image = $request->file('og_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/seo/og/'), $imageName);
            $validated['og_image'] = 'assets/img/seo/og/' . $imageName;
        }

        SeoSetting::create($validated);

        $returnUrl = session()->pull('return_url.seo', route('seo.index'));
        return redirect()->to($returnUrl)->with('success', 'Seo Added To Page!');
    }

    public function edit(SeoSetting $seo)
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/seo')) {
            session(['return_url.seo' => $previousUrl]);
        }

        return Inertia::render('SeoSetting/Edit', [
            'seo' => $seo,
        ]);
    }

    public function update(Request $request, SeoSetting $seo)
    {
        $validated = $request->validate([
            'meta_title' => 'required|string|max:255',
            'url' => [
                'required',
                'string',
                'max:255',
                'unique:seo_settings,url,' . $seo->id,
            ],
            'keywords' => 'nullable|array',
            'keywords.*' => 'string|max:50',
            'search_terms' => 'nullable|array',
            'search_terms.*' => 'string|max:50',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string',
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_type' => 'nullable|string',
            'og_url' => 'nullable|string',
        ]);

        $validated['url'] = ltrim($validated['url'], '/');

        if ($request->hasFile('og_image')) {

            $request->validate([
                'og_image' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($seo->og_image && file_exists(public_path($seo->og_image))) {
                unlink(public_path($seo->og_image));
            }

            $image = $request->file('og_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/seo/og/'), $imageName);
            $validated['og_image'] = 'assets/img/seo/og/' . $imageName;
        }

        $seo->update($validated);

        $returnUrl = session()->pull('return_url.seo', route('seo.index'));
        return redirect()->to($returnUrl)->with('success', 'Seo setting updated successfully!');
    }

    public function destroy(SeoSetting $seo)
    {
        if ($seo->og_image && file_exists(public_path($seo->og_image))) {
            unlink(public_path($seo->og_image));
        }

        $seo->delete();

        return back()->with('success', 'Seo settings deleted successfully!');
    }
}

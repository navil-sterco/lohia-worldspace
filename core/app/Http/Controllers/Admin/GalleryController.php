<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-gallery')->only(['index', 'show']);
        $this->middleware('permission:create-gallery')->only(['create', 'store']);
        $this->middleware('permission:edit-gallery')->only(['edit', 'update']);
        $this->middleware('permission:delete-gallery')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $gallery = Gallery::filter(['search' => $search])->orderBy('id', 'desc')->paginate(12)->withQueryString()->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'created_at' => $item->created_at,
                'type' => $item->type,
                'file_path' => $item->file_path ? asset($item->file_path) : asset('assets/img/placeholder.png')
            ];
        });

        return Inertia::render('Gallery/Index', [
            'gallery' => $gallery,
        ]);
    }

    public function create()
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/gallery')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.gallery' => $previousUrl]);
            }
        }

        return Inertia::render('Gallery/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|array|min:1',
            'file.*' => 'required|file|mimes:jpg,jpeg,png,svg,webp,pdf,mp4,mov,avi,wmv,flv,webm',
            'type' => 'required|in:image,video,file',
        ]);

        $totalSize = 0;

        foreach ($request->file('file') as $file) {
            $totalSize += $file->getSize();
        }

        if ($totalSize > 12 * 1024 * 1024) {
            return back()->withErrors([
                'file' => 'Total selected files size must not exceed 6 MB.'
            ])->withInput();
        }

        $uploadedFiles = [];

        foreach ($request->file('file') as $file) {
            $extension = $file->getClientOriginalExtension();
            $name = time() . '_' . \Str::random(20) . '.' . $extension;
            $filePath = 'assets/img/page-file/' . $name;

            try {
                $file->move(public_path('assets/img/page-file'), $name);

                $uploadedFiles[] = Gallery::create([
                    'file_path' => $filePath,
                    'type' => $request->type,
                    'name' => $request->name,
                ]);
            } catch (\Exception $e) {
                foreach ($uploadedFiles as $uploadedFile) {
                    if (file_exists(public_path($uploadedFile->file_path))) {
                        unlink(public_path($uploadedFile->file_path));
                    }
                    $uploadedFile->delete();
                }

                return back()->with('error', 'Failed to upload files.');
            }
        }

        $returnUrl = session()->pull('return_url.gallery', route('gallery.index'));
        return redirect()->to($returnUrl)->with('success', 'Files uploaded successfully!');
    }

    public function destroy(Gallery $gallery)
    {
        try {
            if (file_exists(public_path($gallery->file_path))) {
                unlink(public_path($gallery->file_path));
            }
            $gallery->delete();

            return back()->with('success', 'File Deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete file: ' . $e->getMessage());
        }
    }
}
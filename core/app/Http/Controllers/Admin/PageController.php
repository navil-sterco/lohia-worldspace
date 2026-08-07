<?php
namespace App\Http\Controllers\Admin;

use App\Models\Tab;
use Inertia\Inertia;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-page')->only(['index', 'show']);
        $this->middleware('permission:create-page')->only(['create', 'store']);
        $this->middleware('permission:edit-page')->only(['edit', 'update']);
        $this->middleware('permission:delete-page')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        if ($search) {

            $matchedPages = Page::query()
                ->with(['tab', 'parent.parent'])
                ->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('tab', function ($query) use ($search) {
                            $query->where('heading', 'like', "%{$search}%");
                        });
                })
                ->get();

            $pageIds = collect();

            foreach ($matchedPages as $page) {

                // Add matched page
                $pageIds->push($page->id);

                // Add all ancestors
                $parent = $page->parent;

                while ($parent) {
                    $pageIds->push($parent->id);
                    $parent = $parent->parent;
                }
            }

            $allPages = Page::query()
                ->with(['tab', 'parent'])
                ->whereIn('id', $pageIds->unique())
                ->get();

            $pages = $this->buildTree($allPages);

            return Inertia::render('Page/Index', [
                'pages' => $pages,
                'searchTerm' => $search,
            ]);
        }

        $pages = Page::query()
            ->whereNull('parent_page_id')
            ->with([
                'tab',
                'children' => function ($query) {
                    $query->with([
                        'tab',
                        'children' => function ($q) {
                            $q->with('tab', 'children');
                        }
                    ])->orderBy('display_order')
                        ->orderBy('title');
                }
            ])
            ->withCount('sections')
            ->orderBy('display_order')
            ->orderBy('id', 'desc')
            ->paginate(30)
            ->withQueryString()
            ->through(function ($page) {
                return $this->formatPageWithChildren($page);
            });

        return Inertia::render('Page/Index', [
            'pages' => $pages,
            'searchTerm' => '',
        ]);
    }

    private function buildTree($pages, $parentId = null)
    {
        $tree = [];

        foreach ($pages as $page) {
            if ($page->parent_page_id == $parentId) {

                $formatted = $this->formatPage($page);

                $children = $this->buildTree($pages, $page->id);

                $formatted['child_pages'] = $children;

                $tree[] = $formatted;
            }
        }

        return $tree;
    }

    private function formatPageWithChildren($page)
    {
        $formatted = $this->formatPage($page);

        if ($page->children && $page->children->count() > 0) {
            $formatted['child_pages'] = $page->children->map(function ($child) {
                return $this->formatPageWithChildren($child);
            })->values();
        } else {
            $formatted['child_pages'] = [];
        }

        return $formatted;
    }

    private function formatPage($page)
    {
        return [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'page_type' => $page->page_type,
            'tab_id' => $page->tab_id,
            'tab' => $page->tab ? [
                'id' => $page->tab->id,
                'heading' => $page->tab->heading,
            ] : null,
            'display_order' => $page->display_order,
            'display_location' => $page->display_location,
            'sections_count' => $page->sections_count ?? 0,
            'is_published' => $page->is_published,
            'created_at' => $page->created_at->format('M d, Y'),
            'parent_title' => $page->parent ? $page->parent->title : null,
            'parent_page_id' => $page->parent_page_id,
            'child_pages' => $page->child_pages ?? [],
        ];
    }

    public function create(Request $request)
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/pages')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.pages' => $previousUrl]);
            }
        }

        return Inertia::render('Page/Create', [
            'tabs' => Tab::orderBy('display_order')->get(),
            'parentPages' => Page::orderBy('title')
                ->get(['id', 'title', 'slug', 'page_type'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(https?:\/\/[a-z0-9.-]+(?::\d+)?(?:\/.*)?|#[a-z0-9_-]+|\/?[a-z0-9_-]+(?:\/[a-z0-9_-]+)*(?:#[a-z0-9_-]+)?)$/i'
            ],
            'page_type' => 'required|in:cms,modular',
            'display_location' => 'nullable|array',
            'display_location.*' => 'in:header,footer,quick-links,sidebar',
            'is_published' => 'boolean',
            'target_blank' => 'boolean',
            'tab_id' => 'nullable|exists:tabs,id',
            'display_order' => 'integer',
            'parent_page_id' => 'nullable|exists:pages,id',
        ]);

        $parentPage = null;
        if (!empty($validated['parent_page_id'])) {
            $parentPage = Page::query()
                ->find($validated['parent_page_id']);

            if (!$parentPage) {
                return back()
                    ->withErrors(['parent_page_id' => 'Selected parent page is invalid.'])
                    ->withInput();
            }

            $validated['tab_id'] = null;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (Page::where('slug', $validated['slug'])->exists()) {

            return back()
                ->withErrors([
                    'slug' => 'The generated slug already exists. Please enter a unique slug.'
                ])
                ->withInput();
        }

        $page = Page::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'page_type' => $validated['page_type'],
            'display_location' => $validated['display_location'] ?? [],
            'is_published' => $validated['is_published'] ?? true,
            'target_blank' => $validated['target_blank'] ?? false,
            'tab_id' => $validated['tab_id'] ?? null,
            'display_order' => $validated['display_order'] ?? 1000,
            'parent_page_id' => $validated['parent_page_id'] ?? null,
        ]);

        $returnUrl = session()->pull('return_url.pages', route('pages.index'));
        return redirect()->to($returnUrl)->with('success', 'Page created successfully!');
    }

    public function show(Page $page)
    {
        return Inertia::render('Page/Show', [
            'page' => $page->load([
                'sections' => function ($query) {
                    $query->orderBy('order');
                },
                'tab'
            ])
        ]);
    }

    public function edit(Request $request, Page $page)
    {
        $previousUrl = url()->previous();
        if ($previousUrl && str_contains($previousUrl, '/pages')) {
            if (
                !str_contains($previousUrl, '/create') &&
                !str_contains($previousUrl, '/edit')
            ) {
                session(['return_url.pages' => $previousUrl]);
            }
        }
        return Inertia::render('Page/Edit', [
            'page' => $page,
            'tabs' => Tab::orderBy('display_order')->get(),
            'parentPages' => Page::where('id', '!=', $page->id)
                ->orderBy('title')
                ->get(['id', 'title', 'slug', 'page_type'])
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(https?:\/\/[a-z0-9.-]+(?::\d+)?(?:\/.*)?|#[a-z0-9_-]+|\/?[a-z0-9_-]+(?:\/[a-z0-9_-]+)*(?:#[a-z0-9_-]+)?)$/i'
            ],
            'page_type' => 'required|in:cms,modular',
            'display_location' => 'nullable|array',
            'display_location.*' => 'in:header,footer,quick-links,sidebar',
            'is_published' => 'boolean',
            'target_blank' => 'boolean',
            'tab_id' => 'nullable|exists:tabs,id',
            'display_order' => 'integer',
            'parent_page_id' => 'nullable|exists:pages,id',
        ]);

        $parentPage = null;
        if (!empty($validated['parent_page_id'])) {
            $parentPage = Page::query()
                ->where('id', '!=', $page->id)
                ->find($validated['parent_page_id']);

            if (!$parentPage) {
                return back()
                    ->withErrors(['parent_page_id' => 'Selected parent page is invalid.'])
                    ->withInput();
            }

            $validated['tab_id'] = null;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        $exists = Page::where('slug', $validated['slug'])->where('id', '!=', $page->id)->exists();
        if ($exists) {
            return back()
                ->withErrors(['slug' => 'The generated slug already exists. Please enter a unique slug.'])
                ->withInput();
        }

        $page->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'page_type' => $validated['page_type'],
            'display_location' => $validated['display_location'] ?? [],
            'is_published' => $validated['is_published'] ?? true,
            'target_blank' => $validated['target_blank'] ?? false,
            'tab_id' => $validated['tab_id'] ?? null,
            'display_order' => $validated['display_order'] ?? 1000,
            'parent_page_id' => $validated['parent_page_id'] ?? null,
        ]);


        $returnUrl = session()->pull('return_url.pages', route('pages.index'));
        return redirect()->to($returnUrl)->with('success', 'Page updated successfully!');
    }

    public function destroy(Page $page)
    {
        $page->slug = 'trash-' . $page->slug . '-' . $page->id;

        $page->save();
        $page->sections()->detach();
        $page->delete();

        return back()->with('success', 'Page deleted successfully!');
    }

    public function togglePublish(Page $page)
    {
        $page->update([
            'is_published' => !$page->is_published
        ]);

        return redirect()->back()->with('success', 'Page publication status updated successfully!');
    }
}
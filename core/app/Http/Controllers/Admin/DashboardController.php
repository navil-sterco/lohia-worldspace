<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Page;
use App\Models\User;
use App\Models\Module;
use App\Models\Gallery;
use App\Models\PageSection;
use App\Models\ContactForm;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPages = Page::count();
        $publishedPages = Page::where('is_published', true)->count();
        $draftPages = Page::where('is_published', false)->count();
        $archivedPages = max(0, $totalPages - $publishedPages - $draftPages);
        $publishedPct = $totalPages > 0 ? round(($publishedPages / $totalPages) * 100, 2) : 0;

        $totalSections = PageSection::count();
        $activeSections = PageSection::where('is_active', true)->count();
        $activeSectionPct = $totalSections > 0 ? round(($activeSections / $totalSections) * 100, 2) : 0;

        $stats = [
            'total_pages' => $totalPages,
            'published_pages' => $publishedPages,
            'draft_pages' => $draftPages,
            'archived_pages' => $archivedPages,
            'tottal_pages_published_percent' => $publishedPct,
            'total_pages_section' => $totalSections,
            'active_sections' => $activeSections,
            'tottal_pages_section_active_percent' => $activeSectionPct,
            'total_images' => Gallery::count(),
            'total_modules' => Module::count(),
            'total_users' => User::count(),
            'total_enquiries' => ContactForm::count(),
        ];

        // Recent Pages
        $recentPages = Page::with('tab')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'is_published' => $page->is_published,
                    'updated_at' => $page->updated_at->diffForHumans(),
                    'tab_name' => $page->tab?->heading ?? 'No Tab',
                ];
            });

        // Recent Sections
        $recentSections = DB::table('page_section')
            ->join('page_sections', 'page_section.page_section_id', '=', 'page_sections.id')
            ->join('pages', 'page_section.page_id', '=', 'pages.id')
            ->select('page_sections.id', 'page_sections.name as title', 'pages.title as page', 'page_section.page_id', 'page_section.page_section_id', 'page_sections.is_active')
            ->orderBy('page_section.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'page' => $section->page,
                    'page_id' => $section->page_id,
                    'section_id' => $section->page_section_id,
                    'status' => $section->is_active ? 'active' : 'inactive',
                ];
            });

        // Recent Enquiries
        $recentEnquiries = ContactForm::orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($enquiry) {
                return [
                    'id' => $enquiry->id,
                    'name' => $enquiry->name,
                    'email' => $enquiry->email,
                    'phone' => $enquiry->phone,
                    'query' => $enquiry->query,
                    'created_at' => $enquiry->created_at->diffForHumans(),
                    'description' => strlen($enquiry->description) > 50 ? substr($enquiry->description, 0, 50) . '...' : $enquiry->description,
                ];
            });

        return Inertia::render('Dashboard/Dashboard', [
            'stats' => $stats,
            'recentPages' => $recentPages,
            'recentSections' => $recentSections,
            'recentEnquiries' => $recentEnquiries,
        ]);
    }
}

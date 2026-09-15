<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsletterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-newsletter')->only('index');
        $this->middleware('permission:delete-newsletter')->only('destroy');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $newsletters = Newsletter::query()
            ->when($search, fn ($query) => $query->where('email', 'like', "%{$search}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Newsletter/Index', [
            'submissions' => $newsletters,
            'searchTerm' => $search ?? '',
        ]);
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return back()->with('success', 'Newsletter subscription deleted successfully!');
    }
}

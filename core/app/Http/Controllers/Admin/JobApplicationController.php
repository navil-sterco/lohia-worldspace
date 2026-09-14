<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-job-application')->only('index');
        $this->middleware('permission:delete-job-application')->only('destroy');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $applications = JobApplication::query()
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%");
            }))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('JobApplication/Index', [
            'submissions' => $applications,
            'searchTerm' => $search ?? '',
        ]);
    }

    public function destroy(JobApplication $jobApplication)
    {
        $jobApplication->delete();

        return back()->with('success', 'Job application deleted successfully!');
    }
}

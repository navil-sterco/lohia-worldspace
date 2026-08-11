<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\ModuleEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobApplicationController extends Controller
{

    public function create(?string $slug = null): View
    {
        $job = null;

        if ($slug) {
            $entry = ModuleEntry::where('slug', $slug)
                ->where('module_id', 4)
                ->first();

            if ($entry) {
                $job = $entry->getDetailData($entry->id);
            }
        }

        return view('dynamic.apply-job', [
            'job' => $job,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_slug' => ['nullable', 'string'],
            'job_title' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'best_time_to_call' => ['nullable', 'in:Morning,Afternoon,Evening'],
            'cv' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvName = time() . '_' . uniqid() . '.' . $cv->getClientOriginalExtension();
            $cv->move(public_path('assets/img/cvs/'), $cvName);
            $validated['cv'] = 'assets/img/cvs/' . $cvName;
        }

        JobApplication::create([
            'job_slug' => $validated['job_slug'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'best_time_to_call' => $validated['best_time_to_call'] ?? null,
            'cv_path' => $validated['cv'] ?? null,
            'cv_original_name' => $cv->getClientOriginalName(),
        ]);

        return view('thank-you.thank-you');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Admin Career Controller - Jobs & Applications
 * 
 * Manage job postings, applications, and statuses
 * 
 * @author SlowWebDev
 */
class CareerController extends Controller
{
    /**
     * List jobs and filterable applications
     */
    public function index(Request $request)
    {
        $jobs = Job::withCount('applications')->latest()->paginate(10);
        $applicationsQuery = JobApplication::with('job');

        if ($request->filled('job')) {
            $applicationsQuery->where('job_id', $request->job);
        }

        if ($request->filled('status')) {
            $applicationsQuery->where('status', $request->status);
        }

        $applications = $applicationsQuery->latest()->paginate(20)->appends($request->query());
        return view('admin.careers.index', compact('jobs', 'applications'));
    }

    public function create()
    {
        $jobsCount = Job::count();
        if ($jobsCount >= 2) {
            return redirect()->route('admin.careers.index')
                           ->with('error', 'Maximum number of job positions (2) has been reached.');
        }
        return view('admin.careers.create');
    }

    public function store(Request $request)
    {
        if (Job::count() >= 2) {
            return redirect()->route('admin.careers.index')
                           ->with('error', 'Maximum number of job positions (2) has been reached.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'requirements' => 'required|string'
        ]);

        try {
            $job = Job::create([
                'title' => $validated['title'],
                'requirements' => array_filter(
                    explode("\n", $validated['requirements']),
                    fn($line) => !empty(trim($line))
                ),
            ]);

            return redirect()
                ->route('admin.careers.index')
                ->with('success', 'Job created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create job: ' . $e->getMessage());
        }
    }

    public function edit(Job $job)
    {
        return view('admin.careers.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
                        'title' => 'required|string|max:255',
            'requirements' => 'required|string',
        ]);

        try {
            $job->update([
                'title' => $validated['title'],
                'requirements' => array_filter(
                    explode("\n", $validated['requirements']),
                    fn($line) => !empty(trim($line))
                ),
            ]);

            return redirect()
                ->route('admin.careers.index')
                ->with('success', 'Job updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update job: ' . $e->getMessage());
        }
    }

    public function destroy(Job $job)
    {
        try {
            $job->delete();
            return redirect()
                ->route('admin.careers.index')
                ->with('success', 'Job deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete job: ' . $e->getMessage());
        }
    }

    public function toggleJobStatus(Job $job)
    {
        try {
            $job->update(['is_active' => !$job->is_active]);
            return redirect()
                ->back()
                ->with('success', 'Job status updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update job status: ' . $e->getMessage());
        }
    }

    public function showApplication(JobApplication $application)
    {
        return redirect()->route('admin.careers.index', ['highlight' => $application->id])
            ->with('info', 'Application for ' . $application->first_name . ' ' . $application->last_name);
    }

    public function downloadCV(JobApplication $application)
    {
        try {
            return Storage::disk('public')->download($application->cv_path, $application->first_name . '_' . $application->last_name . '_CV.pdf');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to download CV: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,contacted,rejected',
        ]);

        try {
            $application->update([
                'status' => $validated['status'],
            ]);

            return redirect()
                ->back()
                ->with('success', 'Application status updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update application status: ' . $e->getMessage());
        }
    }
}
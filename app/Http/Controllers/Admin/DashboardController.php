<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index()
    {
        // Get counts for dashboard stats
        $projectsCount = \App\Models\Project::where('status', 'published')->count();
        $mediaCount = \App\Models\Media::count();
        $jobsCount = \App\Models\Job::where('is_active', true)->count();
        $applicationsCount = \App\Models\JobApplication::where('status', 'pending')->count();
        
        // Get latest records for dashboard tables
        $recentProjects = \App\Models\Project::latest()->take(5)->get();
        $recentApplications = \App\Models\JobApplication::with('job')
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'projectsCount',
            'mediaCount',
            'jobsCount',
            'applicationsCount',
            'recentProjects',
            'recentApplications'
        ));
    }
}

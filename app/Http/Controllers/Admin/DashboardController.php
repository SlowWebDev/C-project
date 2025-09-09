<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\Request;

/**
 * Admin Dashboard Controller
 * 
 * Main admin panel dashboard with statistics and recent activity
 * 
 * @author SlowWebDev
 */
class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics and recent activity
     */
    public function index()
    {
        // Calculate main statistics for dashboard cards
        $projectsCount = Project::where('status', 'published')->count();
        $mediaCount = Media::count();
        $jobsCount = Job::where('is_active', true)->count();
        $applicationsCount = JobApplication::where('status', 'pending')->count();
        
        // Get recent activity for dashboard tables
        $recentProjects = Project::latest()->take(5)->get();
        $recentApplications = JobApplication::with('job')
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

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller 
{
    /**
     * Display the home page
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $projects = Project::where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        $mediaItems = Media::where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        return view('pages.home', compact('projects', 'mediaItems'));
    }
}
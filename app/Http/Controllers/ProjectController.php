<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = [
            'commercial' => Project::where('category', 'commercial')
                ->where('status', 'published')
                ->latest()
                ->get()
                ->map(function ($project) {
                    return [
                        'title' => $project->title,
                        'description' => $project->description,
                        'image' => Storage::url($project->image),
                        'Address' => $project->address ?? 'Location Not Available',
                        'link' => route('projects.show', $project->slug)
                    ];
                }),
            'residential' => Project::where('category', 'residential')
                ->where('status', 'published')
                ->latest()
                ->get()
                ->map(function ($project) {
                    return [
                        'title' => $project->title,
                        'description' => $project->description,
                        'image' => Storage::url($project->image),
                        'Address' => $project->address ?? 'Location Not Available',
                        'link' => route('projects.show', $project->slug)
                    ];
                })
        ];

        return view('pages.projects', compact('projects'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        return view('pages.show_project', compact('project'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = [
            'commercial' => $this->getProjectsByCategory('commercial'),
            'residential' => $this->getProjectsByCategory('residential')
        ];

        return view('pages.projects', compact('projects'));
    }

    /**
     * Get projects by category with formatted data
     */
    private function getProjectsByCategory(string $category)
    {
        return Project::where('category', $category)
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
            });
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        
        return view('pages.show_project', compact('project'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show form for creating a new project
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255|unique:projects',
            'category' => 'required|in:residential,commercial',
            'short_description' => 'required|max:150',
            'description' => 'required',
            'address' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'facilities' => 'nullable|array',
            'facilities.*' => 'exists:facilities,id',
            'status' => 'required|in:draft,published'
        ]);

        try {
            // Generate slug from title
            $validatedData['slug'] = Str::slug($validatedData['title']);

            // Handle main image
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('projects', 'public');
                $validatedData['image'] = $path;
            }

            // Handle gallery images
            if ($request->hasFile('gallery')) {
                $galleryPaths = [];
                foreach ($request->file('gallery') as $image) {
                    $galleryPaths[] = $image->store('projects/gallery', 'public');
                }
                $validatedData['gallery'] = $galleryPaths;
            }

            $project = Project::create($validatedData);
            
            // Sync facilities
            if (isset($validatedData['facilities'])) {
                $project->facilities()->sync($validatedData['facilities']);
            }

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating project: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form for editing project
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255|unique:projects,title,' . $project->id,
            'category' => 'required|in:residential,commercial',
            'description' => 'required',
            'address' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
            'existing_gallery' => 'nullable|array',
            'facilities' => 'nullable|array',
            'facilities.*' => 'exists:facilities,id'
        ]);

        try {
            // Update slug if title changed
            if ($project->title !== $validatedData['title']) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
            }

            // Handle main image
            if ($request->hasFile('image')) {
                // Delete old image
                if ($project->image) {
                    Storage::disk('public')->delete($project->image);
                }
                // Store new image
                $path = $request->file('image')->store('projects', 'public');
                $validatedData['image'] = $path;
            }

            // Handle gallery
            if ($request->has('existing_gallery')) {
                $galleryPaths = $request->input('existing_gallery', []);
            } else {
                $galleryPaths = $project->gallery ?? [];
            }
            
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $galleryPaths[] = $image->store('projects/gallery', 'public');
                }
            }
            
            $validatedData['gallery'] = array_values(array_filter($galleryPaths));
            
            $project->update($validatedData);
            
            // Sync facilities
            $project->facilities()->sync($request->input('facilities', []));

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating project: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show project details
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project)
    {
        try {
            // Delete associated image
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            
            // Delete gallery images
            if ($project->gallery && is_array($project->gallery)) {
                foreach ($project->gallery as $galleryImage) {
                    Storage::disk('public')->delete($galleryImage);
                }
            }
            
            $project->delete();

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting project: ' . $e->getMessage());
        }
    }
}
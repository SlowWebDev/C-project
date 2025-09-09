<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin Project Controller - Real Estate Projects Management
 * 
 * Handles CRUD operations for projects with image galleries and facilities
 * 
 * @author SlowWebDev
 */
class ProjectController extends Controller
{
    /**
     * Display paginated list of all projects
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show project creation form with available facilities
     */
    public function create()
    {
        $availableFacilities = Project::getAvailableFacilities();
        return view('admin.projects.create', compact('availableFacilities'));
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
            'facilities.*' => 'string|max:255',
            'status' => 'required|in:draft,published'
        ]);

        try {
            // Generate unique slug from title
            $baseSlug = Str::slug($validatedData['title']);
            $slug = $baseSlug;
            $counter = 1;
            
            while (Project::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $validatedData['slug'] = $slug;

            // Handle main image with validation
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Additional image validation
                if (!$image->isValid()) {
                    throw new \Exception('Uploaded image is corrupted or invalid.');
                }
                
                $path = $image->store('projects', 'public');
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
            
            // Log project creation
            \Log::info('Project created', [
                'project_id' => $project->id,
                'title' => $project->title,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project "' . $project->title . '" created successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to create project', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $request->except(['image', 'gallery'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Error creating project. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show form for editing project
     */
    public function edit(Project $project)
    {
        $availableFacilities = Project::getAvailableFacilities();
        return view('admin.projects.edit', compact('project', 'availableFacilities'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255|unique:projects,title,' . $project->id,
            'category' => 'required|in:residential,commercial',
            'short_description' => 'required|max:150',
            'description' => 'required',
            'address' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
            'existing_gallery' => 'nullable|array',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255'
        ]);

        try {
            // Update slug if title changed
            if ($project->title !== $validatedData['title']) {
                $baseSlug = Str::slug($validatedData['title']);
                $slug = $baseSlug;
                $counter = 1;
                
                while (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $validatedData['slug'] = $slug;
            }

            // Handle main image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Additional image validation
                if (!$image->isValid()) {
                    throw new \Exception('Uploaded image is corrupted or invalid.');
                }
                
                // Delete old image
                if ($project->image) {
                    Storage::disk('public')->delete($project->image);
                }
                // Store new image
                $path = $image->store('projects', 'public');
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
                    if ($image->isValid()) {
                        $galleryPaths[] = $image->store('projects/gallery', 'public');
                    }
                }
            }
            
            $validatedData['gallery'] = array_values(array_filter($galleryPaths));
            
            $project->update($validatedData);
            
            // Log project update
            \Log::info('Project updated', [
                'project_id' => $project->id,
                'title' => $project->title,
                'user_id' => auth()->id(),
                'changes' => array_keys($validatedData)
            ]);

            return redirect()->route('admin.projects.index')
                ->with('success', 'Project "' . $project->title . '" updated successfully.');
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
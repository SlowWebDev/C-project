<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a listing of the media items
     */
    public function index()
    {
        $mediaItems = Media::latest()->paginate(10);
        return view('admin.media.index', compact('mediaItems'));
    }

    /**
     * Show form for creating a new media item
     */
    public function create()
    {
        return view('admin.media.create');
    }

    /**
     * Store a newly created media item
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255|unique:media',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array|max:20', // Limit gallery to 20 images
            'status' => 'required|in:draft,published'
        ], [
            'image.required' => 'Please select a main image.',
            'image.max' => 'The main image must not be larger than 2MB.',
            'gallery.max' => 'You can upload up to 20 images in the gallery.',
            'gallery.*.max' => 'Each gallery image must not be larger than 2MB.'
        ]);

        try {
            // Generate slug from title
            $validatedData['slug'] = Str::slug($validatedData['title']);

            // Handle main image
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('media', 'public');
                $validatedData['image'] = $path;
            }

            // Handle gallery images
            if ($request->hasFile('gallery')) {
                $galleryPaths = [];
                foreach ($request->file('gallery') as $image) {
                    $galleryPaths[] = $image->store('media/gallery', 'public');
                }
                $validatedData['gallery'] = $galleryPaths;
            }

            Media::create($validatedData);

            return redirect()->route('admin.media.index')
                ->with('success', 'Media created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating media: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Media $medium)
    {
        return view('admin.media.show', ['media' => $medium]);
    }

    public function edit(Media $medium)
    {
        return view('admin.media.edit', ['media' => $medium]);
    }

    public function update(Request $request, Media $medium)
    {
        $validated = $request->validate([
            'title' => 'required|max:255|unique:media,title,' . $medium->id,
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array|max:20',
            'deleted_gallery_images' => 'nullable|string',
            'status' => 'required|in:draft,published'
        ], [
            'image.max' => 'The main image must not be larger than 2MB.',
            'gallery.max' => 'You can upload up to 20 images in the gallery.',
            'gallery.*.max' => 'Each gallery image must not be larger than 2MB.'
        ]);

        try {
            // Update slug if title changed
            if ($medium->title !== $validated['title']) {
                $validated['slug'] = Str::slug($validated['title']);
            }

            // Handle main image
            if ($request->hasFile('image')) {
                if ($medium->image) {
                    Storage::disk('public')->delete($medium->image);
                }
                $validated['image'] = $request->file('image')->store('media', 'public');
            }

            // Handle gallery
            $galleryPaths = $medium->gallery ?? [];
            
            // Handle deleted gallery images
            if ($request->has('deleted_gallery_images')) {
                $deletedImages = explode(',', $request->deleted_gallery_images);
                foreach ($deletedImages as $imagePath) {
                    if (in_array($imagePath, $galleryPaths)) {
                        Storage::disk('public')->delete($imagePath);
                        $galleryPaths = array_diff($galleryPaths, [$imagePath]);
                    }
                }
            }

            // Add new gallery images
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $galleryPaths[] = $image->store('media/gallery', 'public');
                }
            }
            
            $validated['gallery'] = array_values(array_filter($galleryPaths));

            $medium->update($validated);

            return redirect()->route('admin.media.index')
                ->with('success', 'Media updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating media: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified media resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Validate media ID
            if (!$id) {
                throw new \InvalidArgumentException('Media ID is required');
            }

            // Find the media record or fail
            $media = Media::findOrFail($id);

            DB::beginTransaction();

            // Delete main image file if exists
            if ($media->image) {
                try {
                    if (Storage::disk('public')->exists($media->image)) {
                        Storage::disk('public')->delete($media->image);
                    }
                } catch (\Exception $e) {
                    Log::warning("Could not delete main image file: {$media->image}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Delete gallery images if any
            if ($media->gallery && is_array($media->gallery)) {
                foreach ($media->gallery as $galleryImage) {
                    try {
                        if (Storage::disk('public')->exists($galleryImage)) {
                            Storage::disk('public')->delete($galleryImage);
                        }
                    } catch (\Exception $e) {
                        Log::warning("Could not delete gallery image file: {$galleryImage}", [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            // Delete the database record
            if (!$media->delete()) {
                throw new \Exception('Failed to delete media record from database');
            }

            DB::commit();

            return redirect()->route('admin.media.index')
                ->with('success', 'Media deleted successfully.');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Media record not found.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in media deletion', [
                'media_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Error deleting media: ' . $e->getMessage());
        }
    }
}

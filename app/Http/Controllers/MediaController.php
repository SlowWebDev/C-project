<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Media Controller - Public Media Gallery
 * 
 * Display published media items for frontend visitors
 * 
 * @author SlowWebDev
 */
class MediaController extends Controller
{
    /**
     * Show all published media items
     */
    public function index()
    {
        $mediaItems = Media::where('status', 'published')
            ->latest()
            ->get();

        return view('pages.media', compact('mediaItems'));
    }

    public function show($slug)
    {
        $media = Media::where('slug', $slug)->firstOrFail();
        return view('pages.show_media', compact('media'));
    }

}
<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the media items
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
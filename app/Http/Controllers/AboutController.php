<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * About Controller - Company Information Page
 * 
 * Display company description, mission, and vision content
 * 
 * @author SlowWebDev
 */
class AboutController extends Controller
{
    /**
     * Show about page with company information
     */
    public function index()
    {
        // Load about page content from settings
        $aboutSettings = [
            'description_paragraph_1' => Setting::get('about_description_paragraph_1', ''),
            'description_paragraph_2' => Setting::get('about_description_paragraph_2', ''),
            'description_paragraph_3' => Setting::get('about_description_paragraph_3', ''),
            'mission_content' => Setting::get('about_mission_content', ''),
            'vision_content' => Setting::get('about_vision_content', ''),
        ];

        return view('pages.about', compact('aboutSettings'));
    }
}

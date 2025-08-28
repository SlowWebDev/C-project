<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Media;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller 
{
    public function index(): View
    {
        $projects = Project::where('status', 'published')->latest()->take(6)->get();
        $mediaItems = Media::where('status', 'published')->latest()->take(3)->get();

        $homeSettings = [
            'hero_button_text' => Setting::get('home_hero_button_text'),
            'hero_pdf' => Setting::get('home_hero_pdf'),
            'hero_slide_1' => Setting::get('home_hero_slide_1'),
            'hero_slide_2' => Setting::get('home_hero_slide_2'),
            'hero_slide_3' => Setting::get('home_hero_slide_3'),
            
            'stats_years' => Setting::get('home_stats_years'),
            'stats_years_label' => Setting::get('home_stats_years_label'),
            'stats_projects' => Setting::get('home_stats_projects'),
            'stats_projects_label' => Setting::get('home_stats_projects_label'),
            'stats_clients' => Setting::get('home_stats_clients'),
            'stats_clients_label' => Setting::get('home_stats_clients_label'),
            
            'ceo_title' => Setting::get('home_ceo_title'),
            'ceo_name' => Setting::get('home_ceo_name'),
            'ceo_position' => Setting::get('home_ceo_position'),
            'ceo_message' => Setting::get('home_ceo_message'),
            'ceo_image' => Setting::get('home_ceo_image'),
        ];

        return view('pages.home', compact('projects', 'mediaItems', 'homeSettings'));
    }
}

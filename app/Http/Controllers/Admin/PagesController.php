<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{

    /**
     * Show Home Page Management
     */
    public function home()
    {
        $homeSettings = [
            // Hero Section
            'hero_button_text' => Setting::get('home_hero_button_text', 'Download Brochure'),
            'hero_pdf' => Setting::get('home_hero_pdf', ''),
            'hero_slide_1' => Setting::get('home_hero_slide_1', ''),
            'hero_slide_2' => Setting::get('home_hero_slide_2', ''),
            'hero_slide_3' => Setting::get('home_hero_slide_3', ''),
            
            // Statistics (3 circles in why-choose-us)
            'stats_years' => Setting::get('home_stats_years', '20'),
            'stats_years_label' => Setting::get('home_stats_years_label', 'Year'),
            'stats_projects' => Setting::get('home_stats_projects', '60'),
            'stats_projects_label' => Setting::get('home_stats_projects_label', 'Project'),
            'stats_clients' => Setting::get('home_stats_clients', '1200'),
            'stats_clients_label' => Setting::get('home_stats_clients_label', 'Unit'),
            
            // CEO Message Section
            'ceo_title' => Setting::get('home_ceo_title', 'CEO Message'),
            'ceo_name' => Setting::get('home_ceo_name', ''),
            'ceo_message' => Setting::get('home_ceo_message', ''),
            'ceo_image' => Setting::get('home_ceo_image', ''),
        ];

        return view('admin.pages.home', compact('homeSettings'));
    }

    /**
     * Update Home Page Settings
     */
    public function updateHome(Request $request)
    {
        $request->validate([
            // Hero Section
            'hero_button_text' => 'nullable|string|max:50',
            'hero_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'hero_slide_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'hero_slide_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'hero_slide_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            
            // Statistics (3 circles)
            'stats_years' => 'nullable|string|max:10',
            'stats_years_label' => 'nullable|string|max:50',
            'stats_projects' => 'nullable|string|max:10',
            'stats_projects_label' => 'nullable|string|max:50',
            'stats_clients' => 'nullable|string|max:10',
            'stats_clients_label' => 'nullable|string|max:50',
            
            // CEO Message Section
            'ceo_title' => 'nullable|string|max:255',
            'ceo_name' => 'nullable|string|max:255',
            'ceo_message' => 'nullable|string',
            'ceo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Update text settings
            $textFields = [
                // Hero Section
                'home_hero_title', 'home_hero_subtitle', 'home_hero_description', 'home_hero_button_text',
                
                // Why Choose Us Section
                'home_why_choose_title', 'home_why_choose_subtitle', 'home_why_choose_description',
                
                // Statistics
                'home_stats_projects', 'home_stats_projects_label',
                'home_stats_clients', 'home_stats_clients_label',
                'home_stats_years', 'home_stats_years_label',
                'home_stats_awards', 'home_stats_awards_label',
                
                // CEO Message
                'home_ceo_title', 'home_ceo_name', 'home_ceo_message'
            ];

            foreach ($textFields as $field) {
                $requestField = str_replace('home_', '', $field);
                if ($request->has($requestField)) {
                    Setting::set($field, $request->input($requestField));
                }
            }

            // Handle file uploads
            // Hero Image
            if ($request->hasFile('hero_image')) {
                $oldImage = Setting::get('home_hero_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
                
                $path = $request->file('hero_image')->store('pages/home', 'public');
                Setting::set('home_hero_image', $path);
            }

            // Hero PDF
            if ($request->hasFile('hero_pdf')) {
                $oldPdf = Setting::get('home_hero_pdf');
                if ($oldPdf && Storage::disk('public')->exists($oldPdf)) {
                    Storage::disk('public')->delete($oldPdf);
                }
                
                $path = $request->file('hero_pdf')->store('pages/home/pdfs', 'public');
                Setting::set('home_hero_pdf', $path);
            }

            // Hero Slider Images
            foreach (['hero_slide_1', 'hero_slide_2', 'hero_slide_3'] as $slideField) {
                if ($request->hasFile($slideField)) {
                    $oldImage = Setting::get('home_' . $slideField);
                    if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                    
                    $path = $request->file($slideField)->store('pages/home/slider', 'public');
                    Setting::set('home_' . $slideField, $path);
                }
            }

            // CEO Image
            if ($request->hasFile('ceo_image')) {
                $oldImage = Setting::get('home_ceo_image');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
                
                $path = $request->file('ceo_image')->store('pages/home/ceo', 'public');
                Setting::set('home_ceo_image', $path);
            }

            DB::commit();
            return redirect()->route('admin.pages.home')->with('success', 'Home page settings updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update home page settings: ' . $e->getMessage());
        }
    }

    /**
     * Show About Page Management
     */
    public function about()
    {
        $aboutSettings = [
            // Company Description (3 paragraphs)
            'description_paragraph_1' => Setting::get('about_description_paragraph_1', ''),
            'description_paragraph_2' => Setting::get('about_description_paragraph_2', ''),
            'description_paragraph_3' => Setting::get('about_description_paragraph_3', ''),
            
            // Mission Section
            'mission_content' => Setting::get('about_mission_content', ''),
            
            // Vision Section
            'vision_content' => Setting::get('about_vision_content', ''),
        ];

        return view('admin.pages.about', compact('aboutSettings'));
    }

    /**
     * Update About Page Settings
     */
    public function updateAbout(Request $request)
    {
        $request->validate([
            // Company Description paragraphs
            'description_paragraph_1' => 'nullable|string',
            'description_paragraph_2' => 'nullable|string',
            'description_paragraph_3' => 'nullable|string',
            
            // Mission and Vision content
            'mission_content' => 'nullable|string',
            'vision_content' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update text settings
            $textFields = [
                'about_description_paragraph_1',
                'about_description_paragraph_2',
                'about_description_paragraph_3',
                'about_mission_content',
                'about_vision_content'
            ];

            foreach ($textFields as $field) {
                $requestField = str_replace('about_', '', $field);
                if ($request->has($requestField)) {
                    Setting::set($field, $request->input($requestField));
                }
            }

            DB::commit();
            return redirect()->route('admin.pages.about')->with('success', 'About page settings updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update about page settings: ' . $e->getMessage());
        }
    }

    /**
     * Show Contact Page Management
     */
    public function contact()
    {
        $contactSettings = [
            // Contact Information Section
            'contact_title' => Setting::get('contact_information_title', 'Contact Information'),
            'address_title' => Setting::get('contact_address_title', 'Our Address'),
            'address_content' => Setting::get('contact_address_content', ''),
            'email_title' => Setting::get('contact_email_title', 'Email Us'),
            'email_content' => Setting::get('contact_email_content', ''),
            'phone_title' => Setting::get('contact_phone_title', 'Call Us'),
            'phone_content' => Setting::get('contact_phone_content', ''),
            'map_embed_url' => Setting::get('contact_map_embed_url', ''),
        ];

        return view('admin.pages.contact', compact('contactSettings'));
    }

    /**
     * Update Contact Page Settings
     */
    public function updateContact(Request $request)
    {
        $request->validate([
            // Contact Information
            'contact_title' => 'nullable|string|max:255',
            'address_title' => 'nullable|string|max:255',
            'address_content' => 'nullable|string',
            'email_title' => 'nullable|string|max:255',
            'email_content' => 'nullable|email|max:255',
            'phone_title' => 'nullable|string|max:255',
            'phone_content' => 'nullable|string|max:50',
            'map_embed_url' => 'nullable|url',
        ]);

        try {
            DB::beginTransaction();

            // Update text settings with direct mapping
            $fieldMappings = [
                'contact_information_title' => 'contact_title',
                'contact_address_title' => 'address_title',
                'contact_address_content' => 'address_content',
                'contact_email_title' => 'email_title',
                'contact_email_content' => 'email_content',
                'contact_phone_title' => 'phone_title',
                'contact_phone_content' => 'phone_content',
                'contact_map_embed_url' => 'map_embed_url'
            ];

            foreach ($fieldMappings as $dbField => $requestField) {
                if ($request->has($requestField)) {
                    Setting::set($dbField, $request->input($requestField));
                }
            }

            DB::commit();
            return redirect()->route('admin.pages.contact')->with('success', 'Contact page settings updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update contact page settings: ' . $e->getMessage());
        }
    }
}

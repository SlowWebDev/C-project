<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnifiedSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UnifiedSeoController extends Controller
{
    public function index()
    {
        $seo = UnifiedSeo::getInstance();
        $pages = UnifiedSeo::getPages();
        $frequencies = UnifiedSeo::getFrequencies();
        
        return view('admin.seo.unified', compact('seo', 'pages', 'frequencies'));
    }

    public function update(Request $request)
    {
        try {
            $seo = UnifiedSeo::getInstance();
            
            $rules = [];
            $pages = UnifiedSeo::getPages();
            
            // Generate validation rules for all pages
            foreach (array_keys($pages) as $page) {
                $rules[$page . '_title'] = 'nullable|string|max:255';
                $rules[$page . '_meta_title'] = 'nullable|string|max:70';
                $rules[$page . '_meta_description'] = 'nullable|string|max:160';
                $rules[$page . '_meta_keywords'] = 'nullable|string|max:255';
                $rules[$page . '_og_title'] = 'nullable|string|max:60';
                $rules[$page . '_og_description'] = 'nullable|string|max:160';
                $rules[$page . '_canonical'] = 'nullable|url|max:255';
                $rules[$page . '_frequency'] = 'nullable|string|in:always,hourly,daily,weekly,monthly,yearly,never';
                $rules[$page . '_active'] = 'boolean';
                $rules[$page . '_indexable'] = 'boolean';
            }
            
            // Global settings rules
            $rules['site_name'] = 'nullable|string|max:255';
            $rules['default_og_image'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
            
            $validated = $request->validate($rules);
            
            // Handle default og image
            if ($request->hasFile('default_og_image')) {
                if ($seo->default_og_image && Storage::disk('public')->exists($seo->default_og_image)) {
                    Storage::disk('public')->delete($seo->default_og_image);
                }
                $validated['default_og_image'] = $request->file('default_og_image')->store('seo/og-images', 'public');
            } else {
                unset($validated['default_og_image']);
            }
            
            // Update the SEO settings
            $seo->update($validated);
            
            return redirect()->route('admin.seo.unified')
                ->with('success', 'SEO settings updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error saving SEO settings: ' . $e->getMessage()]);
        }
    }

    public function reset()
    {
        $seo = UnifiedSeo::getInstance();
        
        // Keep the ID and timestamps, reset everything else
        $defaults = UnifiedSeo::getDefaults();
        $seo->update($defaults);
        
        return redirect()->route('admin.seo.unified')
            ->with('success', 'SEO settings reset to defaults!');
    }

    public function preview($page)
    {
        $seo = UnifiedSeo::getInstance();
        $pageData = $seo->getPageData($page);
        $pages = UnifiedSeo::getPages();
        
        if (!isset($pages[$page])) {
            abort(404);
        }
        
        return view('admin.seo.preview', compact('pageData', 'page', 'pages'));
    }
}

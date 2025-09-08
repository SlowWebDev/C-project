<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnifiedSeo;
use App\Models\ActivityLog;
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
            
            // Store old values for activity logging
            $oldValues = $seo->toArray();
            
            // Update the SEO settings
            $seo->update($validated);
            
            // Log detailed SEO changes
            $changedFields = [];
            foreach ($validated as $field => $newValue) {
                $oldValue = $oldValues[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changedFields[] = $this->getFieldDisplayName($field);
                }
            }
            
            if (!empty($changedFields)) {
                $description = 'Updated SEO settings: ' . implode(', ', $changedFields) . '. Sitemap regenerated automatically.';
                ActivityLog::logActivity('seo_updated', $seo, null, null, $description);
            }
            
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
        
        // Log the reset action
        ActivityLog::logActivity('seo_reset', $seo, null, null, 'Reset all SEO settings to default values. Sitemap regenerated automatically.');
        
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
    
    private function getFieldDisplayName(string $field): string
    {
        $displayNames = [
            'site_name' => 'Site Name',
            'default_og_image' => 'Default Social Image',
        ];
        
        // Handle page-specific fields
        if (str_contains($field, '_')) {
            $parts = explode('_', $field);
            $pageKey = $parts[0];
            $fieldType = implode('_', array_slice($parts, 1));
            
            $pageNames = UnifiedSeo::getPages();
            $pageName = $pageNames[$pageKey] ?? ucfirst($pageKey);
            
            $fieldNames = [
                'title' => 'Title',
                'meta_title' => 'Meta Title',
                'meta_description' => 'Meta Description',
                'meta_keywords' => 'Meta Keywords',
                'og_title' => 'Social Title',
                'og_description' => 'Social Description',
                'canonical' => 'Canonical URL',
                'frequency' => 'Update Frequency',
                'active' => 'Status',
                'indexable' => 'Search Visibility',
            ];
            
            $fieldDisplayName = $fieldNames[$fieldType] ?? ucfirst(str_replace('_', ' ', $fieldType));
            return $pageName . ' - ' . $fieldDisplayName;
        }
        
        return $displayNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}

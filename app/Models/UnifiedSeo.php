<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UnifiedSeo extends Model
{
    protected $table = 'unified_seo_settings';
    
    protected $fillable = [
        // Home Page
        'home_title', 'home_meta_title', 'home_meta_description', 'home_meta_keywords',
        'home_og_title', 'home_og_description', 'home_canonical',
        'home_priority', 'home_frequency', 'home_active', 'home_indexable',
        
        // About Page  
        'about_title', 'about_meta_title', 'about_meta_description', 'about_meta_keywords',
        'about_og_title', 'about_og_description', 'about_canonical',
        'about_priority', 'about_frequency', 'about_active', 'about_indexable',
        
        // Contact Page
        'contact_title', 'contact_meta_title', 'contact_meta_description', 'contact_meta_keywords',
        'contact_og_title', 'contact_og_description', 'contact_canonical',
        'contact_priority', 'contact_frequency', 'contact_active', 'contact_indexable',
        
        // Projects Page
        'projects_title', 'projects_meta_title', 'projects_meta_description', 'projects_meta_keywords',
        'projects_og_title', 'projects_og_description', 'projects_canonical',
        'projects_priority', 'projects_frequency', 'projects_active', 'projects_indexable',
        
        // Media Page
        'media_title', 'media_meta_title', 'media_meta_description', 'media_meta_keywords',
        'media_og_title', 'media_og_description', 'media_canonical',
        'media_priority', 'media_frequency', 'media_active', 'media_indexable',
        
        // Careers Page
        'careers_title', 'careers_meta_title', 'careers_meta_description', 'careers_meta_keywords',
        'careers_og_title', 'careers_og_description', 'careers_canonical',
        'careers_priority', 'careers_frequency', 'careers_active', 'careers_indexable',
        
        // Global Settings
        'site_name', 'default_og_image'
    ];

    protected $casts = [
        'home_active' => 'boolean', 'home_indexable' => 'boolean', 'home_priority' => 'integer',
        'about_active' => 'boolean', 'about_indexable' => 'boolean', 'about_priority' => 'integer',
        'contact_active' => 'boolean', 'contact_indexable' => 'boolean', 'contact_priority' => 'integer',
        'projects_active' => 'boolean', 'projects_indexable' => 'boolean', 'projects_priority' => 'integer',
        'media_active' => 'boolean', 'media_indexable' => 'boolean', 'media_priority' => 'integer',
        'careers_active' => 'boolean', 'careers_indexable' => 'boolean', 'careers_priority' => 'integer',
        'schema_data' => 'array', 'custom_meta' => 'array'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($model) {
            Cache::forget('seo_instance');
            $model->generateSitemap();
        });
        
        static::created(function ($model) {
            $model->generateSitemap();
        });
    }

    /**
     * Get page SEO data with fallbacks
     */
    public function getPageData($page)
    {
        return [
            'title' => $this->{$page . '_title'} ?: '',
            'meta_title' => $this->{$page . '_meta_title'} ?: $this->{$page . '_title'} ?: '',
            'meta_description' => $this->{$page . '_meta_description'} ?: '',
            'meta_keywords' => $this->{$page . '_meta_keywords'} ?: '',
            'og_title' => $this->{$page . '_og_title'} ?: $this->{$page . '_meta_title'} ?: $this->{$page . '_title'} ?: '',
            'og_description' => $this->{$page . '_og_description'} ?: $this->{$page . '_meta_description'} ?: '',
            'og_image' => $this->default_og_image,
            'canonical' => $this->{$page . '_canonical'} ?: '',
            'priority' => $this->{$page . '_priority'} ?? 5,
            'frequency' => $this->{$page . '_frequency'} ?? 'monthly',
            'active' => $this->{$page . '_active'} ?? true,
            'indexable' => $this->{$page . '_indexable'} ?? true
        ];
    }

    /**
     * Get cached singleton instance
     */
    public static function getInstance()
    {
        return Cache::remember('seo_instance', 3600, function () {
            $instance = static::first();
            if (!$instance) {
                $instance = static::create(static::getDefaults());
            }
            return $instance;
        });
    }

    /**
     * Get default SEO values
     */
    public static function getDefaults()
    {
        return [
            'site_name' => 'C-Project',
            
            // Home defaults 
            'home_priority' => 10,
            'home_frequency' => 'weekly',
            'home_active' => true,
            'home_indexable' => true,
            
            // About defaults 
            'about_priority' => 8,
            'about_frequency' => 'monthly',
            'about_active' => true,
            'about_indexable' => true,
            
            // Contact defaults
            'contact_priority' => 7,
            'contact_frequency' => 'monthly',
            'contact_active' => true,
            'contact_indexable' => true,
            
            // Projects defaults 
            'projects_priority' => 9,
            'projects_frequency' => 'weekly',
            'projects_active' => true,
            'projects_indexable' => true,
            
            // Media defaults 
            'media_priority' => 6,
            'media_frequency' => 'weekly',
            'media_active' => true,
            'media_indexable' => true,
            
            // Careers defaults 
            'careers_priority' => 5,
            'careers_frequency' => 'monthly',
            'careers_active' => true,
            'careers_indexable' => true,
        ];
    }

    /**
     * Get available pages
     */
    public static function getPages()
    {
        return [
            'home' => 'Home Page',
            'about' => 'About Us', 
            'contact' => 'Contact Us',
            'projects' => 'Our Projects',
            'media' => 'Media Gallery',
            'careers' => 'Careers'
        ];
    }

    /**
     * Get sitemap frequencies
     */
    public static function getFrequencies()
    {
        return [
            'always' => 'Always',
            'hourly' => 'Hourly',
            'daily' => 'Daily', 
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'never' => 'Never'
        ];
    }

    /**
     * Generate XML sitemap
     */
    public function generateSitemap()
    {
        try {
            $pages = static::getPages();
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . 
                    ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
                    ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' .
                    ' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
            
            foreach ($pages as $page => $name) {
                $data = $this->getPageData($page);
                if ($data['active'] && $data['indexable']) {
                    $url = $data['canonical'] ?: url($page === 'home' ? '/' : '/' . $page);
                    $xml .= '  <url>' . "\n";
                    $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
                    $xml .= '    <lastmod>' . $this->updated_at->format('Y-m-d\TH:i:s\Z') . '</lastmod>' . "\n";
                    $xml .= '    <changefreq>' . $data['frequency'] . '</changefreq>' . "\n";
                    $xml .= '    <priority>' . number_format($data['priority'] / 10, 1) . '</priority>' . "\n";
                    $xml .= '  </url>' . "\n";
                }
            }
            
            $xml .= '</urlset>';
            
            // Save to public directory
            $sitemapPath = public_path('sitemap.xml');
            file_put_contents($sitemapPath, $xml);
            
            // Log success
            Log::info('Sitemap generated successfully with ' . substr_count($xml, '<url>') . ' URLs');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error generating sitemap: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get image URL with fallback
     */
    public function getImageUrl($imageField)
    {
        if ($this->{$imageField} && Storage::disk('public')->exists($this->{$imageField})) {
            return Storage::url($this->{$imageField});
        }
        
        if ($this->default_og_image && Storage::disk('public')->exists($this->default_og_image)) {
            return Storage::url($this->default_og_image);
        }
        
        return null;
    }
    
    /**
     * Check if page has complete SEO data
     */
    public function isPageSeoComplete($page)
    {
        $data = $this->getPageData($page);
        return !empty($data['meta_title']) && !empty($data['meta_description']);
    }
    
    /**
     * Get SEO completion percentage
     */
    public function getSeoCompletionPercentage()
    {
        $pages = static::getPages();
        $completedPages = 0;
        
        foreach (array_keys($pages) as $page) {
            if ($this->isPageSeoComplete($page)) {
                $completedPages++;
            }
        }
        
        return round(($completedPages / count($pages)) * 100);
    }
    
    /**
     * Scope for active pages
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope for indexable pages
     */
    public function scopeIndexable($query)
    {
        return $query->where('indexable', true);
    }
}

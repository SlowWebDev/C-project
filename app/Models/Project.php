<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Project Model - Real Estate Projects Management
 * 
 * Handles residential and commercial projects with galleries and facilities
 * 
 * @author SlowWebDev
 */
class Project extends Model
{
    /**
     * Fields that can be mass assigned
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'description',
        'image',
        'gallery',
        'facilities',
        'address',
        'status'
    ];

    /**
     * Default values for new projects
     */
    protected $attributes = [
        'status' => 'draft' 
    ];
    
    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'gallery' => 'array',
        'facilities' => 'array'
    ];

    // ======================================================================
    // FACILITY MANAGEMENT METHODS
    // ======================================================================

    /**
     * Get list of all available facilities with icons
     */
    public static function getAvailableFacilities()
    {
        return [
            ['name' => 'Commercial Mall', 'icon' => 'fa-store'],
            ['name' => 'Club House', 'icon' => 'fa-house-flag'],
            ['name' => 'Lagoons', 'icon' => 'fa-water'],
            ['name' => 'Water Features', 'icon' => 'fa-water-ladder'],
            ['name' => 'Running Track', 'icon' => 'fa-person-running'],
            ['name' => 'Parking', 'icon' => 'fa-square-parking'],
            ['name' => 'Security', 'icon' => 'fa-shield-halved'],
            ['name' => 'Kids Area', 'icon' => 'fa-children'],
            ['name' => 'Hypermarket', 'icon' => 'fa-cart-shopping'],
            ['name' => 'Pharmacies', 'icon' => 'fa-prescription-bottle-medical'],
            ['name' => 'Banks', 'icon' => 'fa-building-columns'],
            ['name' => 'Restaurants', 'icon' => 'fa-utensils']
        ];
    }

    /**
     * Check if project has specific facility
     */
    public function hasFacility($facilityName)
    {
        if (!$this->facilities) return false;
        return in_array($facilityName, $this->facilities);
    }

    // ======================================================================
    // ACCESSOR METHODS
    // ======================================================================

    /**
     * Get full URL for project main image
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    // ======================================================================
    // QUERY SCOPES
    // ======================================================================

    /**
     * Get only published projects
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    /**
     * Get only draft projects
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}

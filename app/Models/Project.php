<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Project extends Model
{
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

    protected $attributes = [
        'status' => 'draft' 
    ];
    
    protected $casts = [
        'gallery' => 'array',
        'facilities' => 'array'
    ];

    /**
     * Get default facilities list
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

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

}
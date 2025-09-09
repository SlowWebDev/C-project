<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Media Model - News & Press Assets
 * 
 * Handles media posts with image and gallery support
 * 
 * @author SlowWebDev
 */
class Media extends Model
{
    /**
     * Fields that can be mass assigned
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'gallery',
        'status'
    ];

    /**
     * Default values
     */
    protected $attributes = [
        'status' => 'published'
    ];
    
    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'gallery' => 'array'
    ];

    // ======================================================================
    // ACCESSOR METHODS
    // ======================================================================
    
    /**
     * Get full URL for main image
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    // ======================================================================
    // QUERY SCOPES
    // ======================================================================

    /**
     * Only published media items
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // ======================================================================
    // MODEL BOOT HOOKS
    // ======================================================================

    /**
     * Auto-generate slug from title when creating
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($media) {
            $media->slug = Str::slug($media->title);
        });
    }
}

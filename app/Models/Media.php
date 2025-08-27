<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'gallery',
        'status'
    ];

    protected $attributes = [
        'status' => 'published'
    ];
    
    protected $casts = [
        'gallery' => 'array'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($media) {
            $media->slug = Str::slug($media->title);
        });
    }
}

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
        'address',
        'status'
    ];

    protected $attributes = [
        'status' => 'published'
    ];
    
    protected $casts = [
        'gallery' => 'array'
    ];

    public function facilities()
    {
        return $this->belongsToMany(Facility::class);
    }

    public function hasFacility($facilityId)
    {
        return $this->facilities->contains($facilityId);
    }

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
        
        static::creating(function ($project) {
            $project->slug = Str::slug($project->title);
        });
    }
}
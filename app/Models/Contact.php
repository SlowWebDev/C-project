<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Contact Model - Contact Form Submissions
 * 
 * Handles contact form submissions and project inquiries
 * 
 * @author SlowWebDev
 */
class Contact extends Model
{
    use HasFactory;

    /**
     * Fields that can be mass assigned
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'type', 
        'project_id', 
        'status', 
    ];

    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ======================================================================
    // MODEL RELATIONSHIPS
    // ======================================================================

    /**
     * Get associated project for project inquiries
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // ======================================================================
    // ACCESSOR METHODS
    // ======================================================================

    /**
     * Get contact's full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // ======================================================================
    // QUERY SCOPES
    // ======================================================================

    /**
     * Get general contact form submissions
     */
    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    /**
     * Get project-specific inquiries
     */
    public function scopeProjectInquiry($query)
    {
        return $query->where('type', 'project_inquiry');
    }

    /**
     * Get unread/new contacts
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project associated with this contact 
*/
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Scope for general contacts
     */
    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    /**
     * Scope for project inquiries
     */
    public function scopeProjectInquiry($query)
    {
        return $query->where('type', 'project_inquiry');
    }

    /**
     * Scope for new contacts
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Job Model - Career Opportunities
 * 
 * Manages job postings with applications tracking
 * 
 * @author SlowWebDev
 */
class Job extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Fields that can be mass assigned
     */
    protected $fillable = [
        'title',
        'type',
        'description',
        'requirements',
        'is_active',
        'applications_count'
    ];

    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
        'applications_count' => 'integer'
    ];

    // ======================================================================
    // MODEL RELATIONSHIPS
    // ======================================================================

    /**
     * Job applications relationship
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}

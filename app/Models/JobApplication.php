<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * JobApplication Model - Career Applications
 * 
 * Handles job applications with CV file management
 * 
 * @author SlowWebDev
 */
class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Fields that can be mass assigned
     */
    protected $fillable = [
        'job_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'status'
    ];

    /**
     * Cast attributes to proper types
     */
    protected $casts = [
        'created_at' => 'datetime'
    ];

    // ======================================================================
    // MODEL RELATIONSHIPS
    // ======================================================================

    /**
     * Associated job posting
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}

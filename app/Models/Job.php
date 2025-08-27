<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'description',
        'requirements',
        'is_active',
        'applications_count'
    ];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
        'applications_count' => 'integer'
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}

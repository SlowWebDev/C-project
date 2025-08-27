<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_path',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}

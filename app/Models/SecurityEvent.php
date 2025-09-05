<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'status',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
        'description',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'device_info' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const EVENT_TYPES = [
        'login' => 'Login Attempt',
        'logout' => 'Logout',
        'password_change' => 'Password Changed',
        'email_change' => 'Email Changed',
        'failed_login' => 'Failed Login',
        'account_locked' => 'Account Locked',
        'device_registered' => 'New Device Registered',
        'device_blocked' => 'Device Blocked',
        'suspicious_activity' => 'Suspicious Activity',
    ];

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_BLOCKED = 'blocked';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logEvent(
        string $eventType,
        string $status = self::STATUS_SUCCESS,
        ?int $userId = null,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        $request = request();
        $agent = new Agent();
        
        $deviceInfo = [
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'device_type' => $agent->isDesktop() ? 'desktop' : ($agent->isMobile() ? 'mobile' : 'tablet'),
            'is_robot' => $agent->isRobot(),
        ];

        static::create([
            'user_id' => $userId ?: auth()->id(),
            'event_type' => $eventType,
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_info' => $deviceInfo,
            'location' => null, // Can be enhanced with IP geolocation
            'description' => $description,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    public function getEventTypeDisplayAttribute(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'success' => 'green',
            'failed' => 'red',
            'blocked' => 'orange',
            default => 'gray'
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jenssegers\Agent\Agent;

class DeviceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'browser',
        'operating_system',
        'ip_address',
        'location',
        'is_blocked',
        'last_activity',
        'first_seen',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'last_activity' => 'datetime',
        'first_seen' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getCurrentDeviceId(): string
    {
        $request = request();
        $agent = new Agent();
        
        // Create more robust device fingerprint
        $fingerprint = [
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device_type' => $agent->isDesktop() ? 'desktop' : ($agent->isMobile() ? 'mobile' : 'tablet'),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
        ];
        
        return hash('sha256', implode('|', array_filter($fingerprint)));
    }

    public static function registerOrUpdateDevice(): self
    {
        $request = request();
        $agent = new Agent();
        $deviceId = self::getCurrentDeviceId();
        
        $deviceData = [
            'user_id' => auth()->id(),
            'device_id' => $deviceId,
            'device_type' => $agent->isDesktop() ? 'desktop' : ($agent->isMobile() ? 'mobile' : 'tablet'),
            'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
            'operating_system' => $agent->platform() . ' ' . $agent->version($agent->platform()),
            'ip_address' => $request->ip(),
            'last_activity' => now(),
            'user_agent' => $request->userAgent(),
        ];

        $device = self::where('device_id', $deviceId)
                     ->where('user_id', auth()->id())
                     ->first();

        if ($device) {
            $device->update($deviceData);
        } else {
            $deviceData['first_seen'] = now();
            $deviceData['device_name'] = self::generateDeviceName($agent);
            $device = self::create($deviceData);
            
            SecurityEvent::logEvent('device_registered', SecurityEvent::STATUS_SUCCESS, auth()->id());
        }

        return $device;
    }

    private static function generateDeviceName(Agent $agent): string
    {
        $deviceType = $agent->isDesktop() ? 'Desktop' : ($agent->isMobile() ? 'Mobile' : 'Tablet');
        $browser = $agent->browser();
        $os = $agent->platform();
        
        return "{$deviceType} - {$browser} on {$os}";
    }

    public function isCurrentDevice(): bool
    {
        return $this->device_id === self::getCurrentDeviceId();
    }

    public function block(): void
    {
        $this->update(['is_blocked' => true]);
        SecurityEvent::logEvent('device_blocked', SecurityEvent::STATUS_SUCCESS, $this->user_id);
    }


    public function getLastActivityHumanAttribute(): string
    {
        return $this->last_activity->diffForHumans();
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_blocked) return 'red';
        return 'green';
    }

    public function getStatusTextAttribute(): string
    {
        if ($this->is_blocked) return 'Blocked';
        return 'Active';
    }
}

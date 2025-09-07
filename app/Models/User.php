<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_enabled',
        'two_factor_setup_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_setup_at' => 'datetime',
        ];
    }

    public function generateTwoFactorSecret()
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $this->two_factor_secret = $this->encryptTwoFactorSecret($secret);
        $this->save();
        
        return $secret;
    }

    public function getTwoFactorSecret()
    {
        return $this->two_factor_secret ? $this->decryptTwoFactorSecret($this->two_factor_secret) : null;
    }

    public function verifyTwoFactorCode($code, $allowSetup = false)
    {
        // In setup mode, only require secret exists
        // In normal mode, require both secret and enabled status
        if (!$allowSetup && (!$this->two_factor_secret || !$this->two_factor_enabled)) {
            return false;
        }
        
        if ($allowSetup && !$this->two_factor_secret) {
            return false;
        }
        
        $code = preg_replace('/[^0-9]/', '', $code);
        
        if (strlen($code) !== 6) {
            return false;
        }
        
        $google2fa = new Google2FA();
        $secret = $this->getTwoFactorSecret();
        
        if (!$secret) {
            return false;
        }
        
        // Use window of 2 for better reliability (30-second window each side)
        return $google2fa->verifyKey($secret, $code, 2);
    }

    public function enableTwoFactor()
    {
        $this->update([
            'two_factor_enabled' => true,
            'two_factor_setup_at' => now()
        ]);
    }

    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled && $this->two_factor_secret;
    }

    public function disableTwoFactor()
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_setup_at' => null
        ]);
    }

    private function encryptTwoFactorSecret($secret)
    {
        return Crypt::encrypt($secret);
    }

    private function decryptTwoFactorSecret($encryptedSecret)
    {
        try {
            return Crypt::decrypt($encryptedSecret);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function securityEvents()
    {
        return $this->hasMany(SecurityEvent::class);
    }

    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class);
    }
}

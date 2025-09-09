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

/**
 * User Model - Admin Authentication System
 * 
 * Handles admin user authentication with two-factor security
 * 
 * @author SlowWebDev
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Fields that can be mass assigned
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_enabled',
        'two_factor_setup_at'
    ];

    /**
     * Hide sensitive data from API responses
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Cast attributes to proper types
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

    // ======================================================================
    // TWO-FACTOR AUTHENTICATION METHODS
    // ======================================================================

    /**
     * Generate new 2FA secret key for user
     */
    public function generateTwoFactorSecret()
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $this->two_factor_secret = $this->encryptTwoFactorSecret($secret);
        $this->save();
        
        return $secret;
    }

    /**
     * Get decrypted 2FA secret
     */
    public function getTwoFactorSecret()
    {
        return $this->two_factor_secret ? $this->decryptTwoFactorSecret($this->two_factor_secret) : null;
    }

    /**
     * Verify 6-digit 2FA code
     * @param string $code - 6 digit code from authenticator app
     * @param bool $allowSetup - allow verification during setup process
     */
    public function verifyTwoFactorCode($code, $allowSetup = false)
    {
        // Check if 2FA is properly configured
        if (!$allowSetup && (!$this->two_factor_secret || !$this->two_factor_enabled)) {
            return false;
        }
        
        if ($allowSetup && !$this->two_factor_secret) {
            return false;
        }
        
        // Clean and validate code format
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

    /**
     * Enable 2FA for this user
     */
    public function enableTwoFactor()
    {
        $this->update([
            'two_factor_enabled' => true,
            'two_factor_setup_at' => now()
        ]);
    }

    /**
     * Check if 2FA is fully enabled and configured
     */
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled && $this->two_factor_secret;
    }

    /**
     * Disable 2FA and clear all related data
     */
    public function disableTwoFactor()
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_setup_at' => null
        ]);
    }

    /**
     * Encrypt 2FA secret for database storage
     */
    private function encryptTwoFactorSecret($secret)
    {
        return Crypt::encrypt($secret);
    }

    /**
     * Decrypt 2FA secret from database
     */
    private function decryptTwoFactorSecret($encryptedSecret)
    {
        try {
            return Crypt::decrypt($encryptedSecret);
        } catch (\Exception $e) {
            return null;
        }
    }

    // ======================================================================
    // MODEL RELATIONSHIPS
    // ======================================================================

    /**
     * User activity logs relationship
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * User security events relationship
     */
    public function securityEvents()
    {
        return $this->hasMany(SecurityEvent::class);
    }

    /**
     * User device sessions relationship
     */
    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class);
    }
}

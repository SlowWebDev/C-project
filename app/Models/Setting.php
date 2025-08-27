<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // Define logo types as constants
    const LOGO_DEFAULT = 'logo';
    const LOGO_LIGHT = 'logo-light';
    const LOGO_DARK = 'logo-footer';

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getLogo($type = self::LOGO_DEFAULT)
    {
        $defaultPath = $type === 'logo-footer' ? 'assets/images/logo-footer.png' : 'assets/images/logo.png';
        $logoPath = self::get($type, $defaultPath);
        
        if (str_starts_with($logoPath, 'storage/')) {
            $filePath = public_path($logoPath);
            if (file_exists($filePath)) {
                // Add timestamp to force browser cache refresh
                return asset($logoPath) . '?v=' . filemtime($filePath);
            }
        }
        
        if (str_starts_with($logoPath, 'assets/')) {
            return asset($logoPath);
        }
        
        return asset($defaultPath);
    }
}

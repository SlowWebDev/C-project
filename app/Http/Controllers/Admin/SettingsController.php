<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Admin Settings Controller - Global Site Settings
 * 
 * Handles logo uploads and footer/social settings
 * 
 * @author SlowWebDev
 */
class SettingsController extends Controller
{
    /**
     * Maximum allowed file size in bytes (2MB)
     */
    const MAX_FILE_SIZE = 2048000;

    /**
     * Allowed file types
     */
    const ALLOWED_TYPES = ['jpeg', 'png', 'jpg', 'gif', 'svg'];

    /**
     * Execute an Artisan command
     *
     * @param string $command
     * @return bool
     * @throws \Exception
     */
    protected function executeArtisanCommand($command)
    {
        try {
            $output = [];
            $return_var = 0;
            
            exec('cd ' . base_path() . ' && php artisan ' . $command, $output, $return_var);
            
            if ($return_var !== 0) {
                Log::error('Artisan command failed', [
                    'command' => $command,
                    'output' => $output,
                    'return_var' => $return_var
                ]);
                throw new \Exception('Failed to execute artisan command');
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error executing artisan command', [
                'command' => $command,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update site logo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLogo(Request $request)
    {
        try {
            Log::info('Starting logo update process');
            
            // Validate request
            $validated = $request->validate([
                'logo' => 'required|file|mimes:' . implode(',', self::ALLOWED_TYPES) . '|max:' . (self::MAX_FILE_SIZE / 1000),
                'type' => 'required|in:logo,logo-footer'
            ]);

            $file = $request->file('logo');
            if (!$file || !$file->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            $type = $request->input('type');
            $extension = $file->getClientOriginalExtension();
            $filename = $type . '.' . $extension;
            
            $this->ensureStorageStructure();

            $uploadResult = $this->handleFileUpload($file, $filename);
            
            if (!$uploadResult['success']) {
                throw new \Exception($uploadResult['message']);
            }

            // Update database
            $this->updateDatabase($type, $filename);

            // Generate response URL with cache buster
            $fileUrl = $this->generateFileUrl($filename);
            
            Log::info('Logo updated successfully', [
                'type' => $type,
                'path' => $uploadResult['path'],
                'url' => $fileUrl
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logo has been updated successfully!',
                'logo_url' => $fileUrl
            ]);
            
        } catch (\Exception $e) {
            Log::error('Logo update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update logo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ensure storage structure exists
     *
     * @return void
     * @throws \Exception
     */
    private function ensureStorageStructure()
    {
        if (!file_exists(public_path('storage'))) {
            $this->executeArtisanCommand('storage:link');
        }

        $logosPath = public_path('storage/logos');
        if (!file_exists($logosPath)) {
            if (!mkdir($logosPath, 0775, true)) {
                throw new \Exception('Failed to create logos directory');
            }
        }
    }

    /**
     * Handle file upload process
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $filename
     * @return array
     */
    private function handleFileUpload($file, $filename)
    {
        try {
            $destination = 'storage/logos/' . $filename;
            $fullPath = public_path($destination);

            // Remove old file if exists
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Move new file
            if (!$file->move(public_path('storage/logos'), $filename)) {
                return [
                    'success' => false,
                    'message' => 'Failed to move uploaded file'
                ];
            }

            // Set permissions
            chmod($fullPath, 0664);

            return [
                'success' => true,
                'path' => $fullPath
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update database with new logo information
     *
     * @param string $type
     * @param string $filename
     * @return void
     */
    private function updateDatabase($type, $filename)
    {
        Setting::updateOrCreate(
            ['key' => $type],
            ['value' => 'storage/logos/' . $filename]
        );
    }

    /**
     * Generate public URL for the file with cache buster
     *
     * @param string $filename
     * @return string
     */
    private function generateFileUrl($filename)
    {
        return asset('storage/logos/' . $filename) . '?t=' . time();
    }

    /**
     * Update footer settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'footer_description' => 'required|string|max:500',
            'copyright_text' => 'required|string|max:100',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'whatsapp_url' => 'nullable|url|max:255',
        ]);

        // Update footer content
        Setting::set('footer_description', $validated['footer_description']);
        Setting::set('copyright_text', $validated['copyright_text']);
        
        // Update social media links
        Setting::set('social_facebook', $validated['facebook_url']);
        Setting::set('social_instagram', $validated['instagram_url']);
        Setting::set('social_linkedin', $validated['linkedin_url']);
        Setting::set('social_tiktok', $validated['tiktok_url']);
        Setting::set('social_whatsapp', $validated['whatsapp_url']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Footer settings updated successfully!');
    }
}

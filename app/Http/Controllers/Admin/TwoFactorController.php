<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class TwoFactorController extends Controller
{
    
    /**
     * Show 2FA setup page
     * Only accessible to authenticated users without 2FA enabled
     */
    public function setup()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }
        
        // If already 2FA verified, redirect to dashboard
        if (request()->session()->get('2fa_verified')) {
            return redirect()->route('admin.dashboard');
        }
        
        $user = Auth::user();
        
        // If user already has 2FA enabled, redirect to verification
        if ($user->hasTwoFactorEnabled() && !session('2fa_verified')) {
            return redirect()->route('admin.2fa.show-verify')
                ->with('info', 'Please verify your 2FA code to continue.');
        }
        
        // If user has 2FA enabled and verified, go to dashboard
        if ($user->hasTwoFactorEnabled() && session('2fa_verified')) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'You are already authenticated with 2FA.');
        }
        
        try {
            // Generate secret if not exists
            if (!$user->getTwoFactorSecret()) {
                $user->generateTwoFactorSecret();
            }
            
            $secret = $user->getTwoFactorSecret();
            $google2fa = new Google2FA();
            
            // Create QR code URL for the authenticator app
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name', 'Admin Panel'),
                $user->email,
                $secret
            );
            
            // Generate QR Code image
            $qrCodeImage = $this->generateQRCodeImage($qrCodeUrl);
            
            // Backup QR code using simple method
            $qrCodeBackup = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);
            
            Log::info('2FA setup page accessed', ['user_id' => $user->id]);
            
            return view('admin.auth.2fa-setup', compact('secret', 'qrCodeUrl', 'qrCodeImage', 'qrCodeBackup'));
            
        } catch (\Exception $e) {
            Log::error('2FA setup error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.login')
                ->with('error', 'Unable to setup 2FA. Please try again.');
        }
    }
    
    /**
     * Verify 2FA code and enable 2FA for the user
     */
    public function verify(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'code' => 'required|string|min:6|max:6'
        ], [
            'code.required' => 'Verification code is required.',
            'code.min' => 'Verification code must be 6 digits.',
            'code.max' => 'Verification code must be 6 digits.'
        ]);
        
        $user = Auth::user();
        $code = trim($request->code);
        
        // Additional validation - only digits
        if (!preg_match('/^\d{6}$/', $code)) {
            return back()->withErrors(['code' => 'Code must contain only numbers.']);
        }
        
        try {
            // Verify the provided code (allow setup mode)
            if ($user->verifyTwoFactorCode($code, true)) {
                // Enable 2FA for the user
                $user->enableTwoFactor();
                
                // Mark 2FA as verified in the session
                $request->session()->put('2fa_verified', true);
                
                Log::info('2FA enabled successfully', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Two-factor authentication enabled successfully! Your account is now more secure.');
            }
            
            // Log failed verification attempt
            Log::warning('2FA verification failed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'code_length' => strlen($code)
            ]);
            
            return back()->withErrors([
                'code' => 'Invalid verification code. Please check your authenticator app and try again.'
            ])->withInput();
            
        } catch (\Exception $e) {
            Log::error('2FA verification error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors([
                'code' => 'An error occurred during verification. Please try again.'
            ]);
        }
    }
    
    /**
     * Show verification page for existing users with 2FA enabled
     */
    public function showVerify()
    {
        $user = Auth::user();
        
        // If user doesn't have 2FA enabled, redirect to setup
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }
        
        // If already verified in session, go to dashboard
        if (session('2fa_verified')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.2fa-verify');
    }
    
    /**
     * Process verification for existing users with 2FA enabled
     */
    public function processVerify(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'code' => 'required|string|min:6|max:6'
        ], [
            'code.required' => 'Verification code is required.',
            'code.min' => 'Verification code must be 6 digits.',
            'code.max' => 'Verification code must be 6 digits.'
        ]);
        
        $user = Auth::user();
        $code = trim($request->code);
        
        // Additional validation - only digits
        if (!preg_match('/^\d{6}$/', $code)) {
            return back()->withErrors(['code' => 'Code must contain only numbers.']);
        }
        
        try {
            // Verify the provided code (normal verification mode)
            if ($user->verifyTwoFactorCode($code, false)) {
                // Mark 2FA as verified in the session
                $request->session()->put('2fa_verified', true);
                
                Log::info('2FA verification successful', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Successfully authenticated! Welcome to the admin panel.');
            }
            
            // Log failed verification attempt
            Log::warning('2FA verification failed', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'code_length' => strlen($code)
            ]);
            
            return back()->withErrors([
                'code' => 'Invalid verification code. Please check your authenticator app and try again.'
            ])->withInput();
            
        } catch (\Exception $e) {
            Log::error('2FA verification error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors([
                'code' => 'An error occurred during verification. Please try again.'
            ]);
        }
    }
    
    
    /**
     * Generate QR Code image using chillerlan/php-qrcode library
     * 
     * @param string $url The QR code data URL
     * @return string|null Base64 encoded PNG image or null on failure
     */
    private function generateQRCodeImage($url)
    {
        try {
            // Simplified QR code options
            $options = new QROptions();
            $options->outputType = QRCode::OUTPUT_IMAGE_PNG;
            $options->eccLevel = QRCode::ECC_L;
            $options->scale = 6;
            $options->imageBase64 = true;
            
            // Generate the QR code
            $qrcode = new QRCode($options);
            $qrImage = $qrcode->render($url);
            
            Log::info('QR Code generated successfully');
            return $qrImage;
            
        } catch (\Throwable $e) {
            // Log the error with context
            Log::error('QR Code generation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $url
            ]);
            
            // Return null so the template can handle fallback
            return null;
        }
    }
}

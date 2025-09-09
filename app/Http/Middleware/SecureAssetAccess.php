<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Secure Asset Access Middleware - File Security
 * 
 * Protects sensitive files and validates file access permissions
 * 
 * @author SlowWebDev
 */
class SecureAssetAccess
{
    /**
     * Protected file paths that require authentication
     */
    private array $protectedPaths = [
        'media',
        'private',
        'uploads',
        'config',
        'logs',
        '.env',
        '.git',
        '.htaccess',
        'vendor',
        'app',
        'database'
    ];

    /**
     * Forbidden file patterns (case insensitive)
     */
    private array $forbiddenPatterns = [
        '\.php$',
        '\.phtml$',
        '\.php\d+$',
        '\.php~$',
        '\.ph3$',
        '\.pht$',
        '\.cgi$',
        '\.asp$',
        '\.aspx$',
        '\.jsp$',
        '\.env',
        'wp-config',
        'config\..*',
        '\.htaccess',
        '\.git',
        '#.*#',    // Temp files
        '\.bak$',  // Backup files
        '\.old$',  // Old files
        '\.save$', // Save files
        '\.swp$',  // Swap files
    ];

    /**
     * Allowed file types for public access
     */
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'text/plain',
        'application/zip',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword'
    ];

    /**
     * Handle an incoming request.
     * Secures access to storage files by:
     * 1. Checking if the path is protected
     * 2. Validating file existence
     * 3. Checking file mime type
     * 4. Verifying user permissions
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Only handle storage access requests
        if (!str_starts_with($path, 'storage/')) {
            return $next($request);
        }

        // Extract relative path
        $relativePath = str_replace('storage/', '', $path);

        // Block access to suspicious files
        foreach ($this->forbiddenPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $relativePath)) {
                abort(403, 'Access denied');
            }
        }

        // Check if this is a protected path
        if ($this->isProtectedPath($relativePath)) {
            if (!Auth::check()) {
                abort(401, 'Authentication required to access this resource');
            }

            // Additional security for authenticated users
            if (!$this->userHasPermission($relativePath)) {
                abort(403, 'You do not have permission to access this resource');
            }
        }

        // Validate file existence
        if (!Storage::exists($relativePath)) {
            abort(404, 'File not found');
        }

        // Validate file type
        $mimeType = Storage::mimeType($relativePath);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            abort(403, 'Invalid file type');
        }

        // Check for suspicious content in files
        if ($this->containsSuspiciousContent($relativePath)) {
            abort(403, 'Invalid file content');
        }

        return $next($request);
    }

    /**
     * Check if the path is protected
     */
    private function isProtectedPath(string $path): bool
    {
        foreach ($this->protectedPaths as $protectedPath) {
            if (str_starts_with($path, $protectedPath . '/')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if authenticated user has permission to access the file
     */
    private function userHasPermission(string $path): bool
    {
        $user = Auth::user();
        
        // Only admins can access certain paths
        $adminOnlyPaths = ['private/', 'logs/', 'config/'];
        foreach ($adminOnlyPaths as $adminPath) {
            if (str_starts_with($path, $adminPath) && !$user->isAdmin()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check file content for suspicious patterns
     */
    private function containsSuspiciousContent(string $path): bool
    {
        // Skip large files and non-text files
        $mimeType = Storage::mimeType($path);
        if (!str_starts_with($mimeType, 'text/')) {
            return false;
        }

        $content = Storage::get($path);
        
        // Check for common malicious patterns
        $suspiciousPatterns = [
            'eval\s*\(',
            'base64_decode\s*\(',
            'shell_exec\s*\(',
            'system\s*\(',
            'passthru\s*\(',
            'exec\s*\(',
            '`.*`',  // Backtick operator
            '\$_GET\s*\[.*\].*\(.*\)',  // Execution of GET parameters
            '\$_POST\s*\[.*\].*\(.*\)',  // Execution of POST parameters
            '\$_REQUEST\s*\[.*\].*\(.*\)',  // Execution of REQUEST parameters
            'file_get_contents\s*\(\s*\$_(GET|POST|REQUEST)',
            'include\s*\(\s*\$_(GET|POST|REQUEST)',
            'require\s*\(\s*\$_(GET|POST|REQUEST)',
            'assert\s*\(',
            'create_function\s*\(',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $content)) {
                return true;
            }
        }

        return false;
    }
}

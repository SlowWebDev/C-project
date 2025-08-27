<?php

namespace App\Http\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FileSecurityChecker
{
    /**
     * PDF Magic bytes signatures
     */
    private const PDF_SIGNATURES = [
        "\x25\x50\x44\x46", // %PDF
    ];

    /**
     * Maximum PDF file size (5MB)
     */
    private const MAX_PDF_SIZE = 5 * 1024 * 1024;

    /**
     * Minimum PDF file size (1KB)
     */
    private const MIN_PDF_SIZE = 1024;

    /**
     * Maximum file name length
     */
    private const MAX_FILENAME_LENGTH = 255;

    /**
     * Dangerous patterns to check in PDF content
     */
    private const DANGEROUS_PATTERNS = [
        // JavaScript patterns
        '/\/JavaScript/i',
        '/\/JS/i',
        '/javascript:/i',
        '/vbscript:/i',
        '/<script[^>]*>/i',
        '/eval\s*\(/i',
        '/Function\s*\(/i',
        '/setTimeout\s*\(/i',
        '/setInterval\s*\(/i',
        
        // PDF Action patterns
        '/\/Action/i',
        '/\/OpenAction/i',
        '/\/Launch/i',
        '/\/URI/i',
        '/\/SubmitForm/i',
        '/\/ImportData/i',
        '/\/GoTo/i',
        '/\/GoToR/i',
        '/\/Thread/i',
        '/\/Movie/i',
        '/\/Sound/i',
        '/\/Hide/i',
        '/\/Named/i',
        '/\/SetOCGState/i',
        '/\/Rendition/i',
        '/\/Trans/i',
        '/\/3D/i',
        '/\/RichMedia/i',
        
        // Encryption and Security
        '/\/Encrypt/i',
        '/\/P -/i',
        '/\/V [4-9]/i', // High encryption versions
        
        // System commands
        '/\bshell\b/i',
        '/\bcmd\b/i',
        '/\bpowershell\b/i',
        '/\bexec\b/i',
        '/\bsystem\b/i',
        '/\bpassthru\b/i',
        '/proc_open/i',
        '/popen/i',
        '/shell_exec/i',
        
        // ActiveX and embeds
        '/activex/i',
        '/\bobject\b/i',
        '/\bembed\b/i',
        '/\bapplet\b/i',
        
        // Suspicious URLs
        '/http:\/\/[^\s]+\.(exe|bat|cmd|scr|pif|com)/i',
        '/https:\/\/[^\s]+\.(exe|bat|cmd|scr|pif|com)/i',
        '/ftp:\/\/[^\s]+\.(exe|bat|cmd|scr|pif|com)/i',
        
        // Base64 encoded suspicious content
        '/data:application\/octet-stream/i',
        '/data:application\/x-msdownload/i',
        
        // PDF structure manipulation
        '/\/Linearized/i',
        '/\/XRefStm/i',
        
        // Form manipulation
        '/\/AcroForm/i',
        '/\/AA/i', // Additional actions
        '/\/OpenAction/i',
    ];

    /**
     * Blocked file name patterns
     */
    private const BLOCKED_FILENAME_PATTERNS = [
        '/\.(exe|bat|cmd|scr|pif|com|msi|dll|jar)$/i',
        '/[<>:"|?*\\\\]/i', // Windows invalid chars
        '/\x00/', // Null bytes
        '/\.\./', // Directory traversal
        '/^\./i', // Hidden files
        '/^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])$/i', // Windows reserved names
    ];

    /**
     * Validate PDF file security
     */
    public static function validatePDF(UploadedFile $file): array
    {
        try {
            // Check file size
            if ($file->getSize() > self::MAX_PDF_SIZE) {
                return [
                    'valid' => false,
                    'error' => 'File size exceeds 5MB limit'
                ];
            }

            // Check file extension
            if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
                return [
                    'valid' => false,
                    'error' => 'Only PDF files are allowed'
                ];
            }

            // Check MIME type
            if ($file->getMimeType() !== 'application/pdf') {
                return [
                    'valid' => false,
                    'error' => 'Invalid file type. Only PDF files are allowed'
                ];
            }

            // Check file signature (magic bytes)
            if (!self::hasValidPDFSignature($file)) {
                return [
                    'valid' => false,
                    'error' => 'Invalid PDF file structure'
                ];
            }

            // Scan for dangerous content
            if (self::containsDangerousContent($file)) {
                Log::warning('Potentially dangerous PDF upload attempt', [
                    'filename' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'ip' => request()->ip(),
                ]);

                return [
                    'valid' => false,
                    'error' => 'PDF file contains potentially dangerous content'
                ];
            }

            return [
                'valid' => true,
                'error' => null
            ];

        } catch (\Exception $e) {
            Log::error('File security check failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);

            return [
                'valid' => false,
                'error' => 'File validation failed'
            ];
        }
    }

    /**
     * Check if file has valid PDF signature
     */
    private static function hasValidPDFSignature(UploadedFile $file): bool
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 4);
        fclose($handle);

        foreach (self::PDF_SIGNATURES as $signature) {
            if (strpos($header, $signature) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scan PDF content for dangerous patterns
     */
    private static function containsDangerousContent(UploadedFile $file): bool
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) {
            return false;
        }

        // Read first 50KB for security scan (enough to detect most threats)
        $content = fread($handle, 50 * 1024);
        fclose($handle);

        // Convert to text for pattern matching
        $textContent = self::extractTextFromBinary($content);

        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            if (preg_match($pattern, $textContent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract readable text from binary content
     */
    private static function extractTextFromBinary(string $content): string
    {
        // Remove null bytes and non-printable characters except spaces
        $cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\xFF]/', '', $content);
        return strtolower($cleaned);
    }

    /**
     * Generate secure filename
     */
    public static function generateSecureFilename(UploadedFile $file): string
    {
        $timestamp = time();
        $randomString = bin2hex(random_bytes(8));
        $hash = substr(hash('sha256', $file->getClientOriginalName() . $timestamp), 0, 8);
        
        return "cv_{$timestamp}_{$randomString}_{$hash}.pdf";
    }

    /**
     * Additional virus-like patterns check
     */
    public static function hasVirusLikePatterns(UploadedFile $file): bool
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) {
            return false;
        }

        $chunk = fread($handle, 1024 * 100); // Read first 100KB
        fclose($handle);

        // Known malicious patterns
        $virusPatterns = [
            '/MZ/',                    // PE executable header
            '/\x4D\x5A/',             // PE executable
            '/PK\x03\x04/',           // ZIP archive (could contain executables)
            '/Rar!/',                 // RAR archive
            '/7z\xBC\xAF\x27\x1C/',  // 7-Zip archive
            '/\x50\x4B\x07\x08/',     // ZIP with data descriptor
            '/begin \d+/',            // UUencoded files
            '/\xFF\xD8\xFF\xE0/',     // JPEG (shouldn't be in PDF)
            '/GIF8[79]a/',            // GIF (shouldn't be in PDF)
            '/\x89PNG/',              // PNG (shouldn't be in PDF)
        ];

        foreach ($virusPatterns as $pattern) {
            if (preg_match($pattern, $chunk)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate filename security
     */
    public static function validateFileName(string $filename): array
    {
        // Check filename length
        if (strlen($filename) > self::MAX_FILENAME_LENGTH) {
            return [
                'valid' => false,
                'error' => 'Filename is too long'
            ];
        }

        // Check for blocked patterns
        foreach (self::BLOCKED_FILENAME_PATTERNS as $pattern) {
            if (preg_match($pattern, $filename)) {
                return [
                    'valid' => false,
                    'error' => 'Invalid filename format'
                ];
            }
        }

        return [
            'valid' => true,
            'error' => null
        ];
    }

    /**
     * Rate limiting check based on IP
     */
    public static function checkRateLimit(string $ip, int $maxAttempts = 5, int $minutes = 10): array
    {
        $key = 'file_upload_attempts_' . $ip;
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return [
                'allowed' => false,
                'error' => 'Too many upload attempts. Please wait before trying again.'
            ];
        }

        // Increment attempts
        Cache::put($key, $attempts + 1, now()->addMinutes($minutes));

        return [
            'allowed' => true,
            'error' => null
        ];
    }

    /**
     * Enhanced PDF structure validation
     */
    public static function validatePDFStructure(UploadedFile $file): array
    {
        try {
            $handle = fopen($file->getPathname(), 'rb');
            if (!$handle) {
                return [
                    'valid' => false,
                    'error' => 'Cannot read file'
                ];
            }

            // Read the entire file for structure analysis
            $content = fread($handle, $file->getSize());
            fclose($handle);

            // Check for minimum file size
            if ($file->getSize() < self::MIN_PDF_SIZE) {
                return [
                    'valid' => false,
                    'error' => 'PDF file is too small to be valid'
                ];
            }

            // Check for PDF trailer
            if (!preg_match('/%%EOF/i', $content)) {
                return [
                    'valid' => false,
                    'error' => 'Invalid PDF structure - missing EOF marker'
                ];
            }

            // Check for xref table
            if (!preg_match('/xref/i', $content) && !preg_match('/XRefStm/i', $content)) {
                return [
                    'valid' => false,
                    'error' => 'Invalid PDF structure - missing cross-reference table'
                ];
            }

            // Check for suspicious object count (potential zip bomb)
            $objCount = preg_match_all('/\d+ \d+ obj/', $content);
            if ($objCount > 10000) {
                return [
                    'valid' => false,
                    'error' => 'PDF contains too many objects - potential security threat'
                ];
            }

            return [
                'valid' => true,
                'error' => null
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'PDF structure validation failed'
            ];
        }
    }

    /**
     * Comprehensive file validation
     */
    public static function validateFile(UploadedFile $file, string $ip = null): array
    {
        // Rate limiting check
        if ($ip) {
            $rateCheck = self::checkRateLimit($ip);
            if (!$rateCheck['allowed']) {
                return [
                    'valid' => false,
                    'error' => $rateCheck['error']
                ];
            }
        }

        // Filename validation
        $filenameCheck = self::validateFileName($file->getClientOriginalName());
        if (!$filenameCheck['valid']) {
            return $filenameCheck;
        }

        // PDF validation
        $pdfCheck = self::validatePDF($file);
        if (!$pdfCheck['valid']) {
            return $pdfCheck;
        }

        // PDF structure validation
        $structureCheck = self::validatePDFStructure($file);
        if (!$structureCheck['valid']) {
            return $structureCheck;
        }

        // Virus-like patterns check
        if (self::hasVirusLikePatterns($file)) {
            return [
                'valid' => false,
                'error' => 'File contains suspicious patterns'
            ];
        }

        return [
            'valid' => true,
            'error' => null
        ];
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class OptimizeImages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only process HTML responses
        if ($response instanceof \Illuminate\Http\Response && 
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            
            $content = $response->getContent();
            
            // Add preload hints for critical resources
            $content = $this->addPreloadHints($content);
            
            // Optimize images in HTML content
            $content = $this->optimizeImagesInHtml($content);
            
            // Add performance optimizations
            $content = $this->addPerformanceOptimizations($content);
            
            $response->setContent($content);
        }
        
        return $response;
    }
    
    /**
     * Add preload hints for critical resources
     */
    private function addPreloadHints(string $content): string
    {
        $preloadHints = '';
        
        // Preload critical CSS
        if (preg_match_all('/<link[^>]+href=["\']([^"\']*\.css)[^>]*>/', $content, $matches)) {
            foreach ($matches[1] as $cssFile) {
                if (str_contains($cssFile, 'app.css') || str_contains($cssFile, 'main.css')) {
                    $preloadHints .= '<link rel="preload" href="' . $cssFile . '" as="style">' . "\n";
                }
            }
        }
        
        // Preload critical fonts
        $preloadHints .= '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        $preloadHints .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        
        // Insert preload hints in head
        if (!empty($preloadHints)) {
            $content = str_replace('<head>', '<head>' . "\n" . $preloadHints, $content);
        }
        
        return $content;
    }
    
    /**
     * Optimize images in HTML content
     */
    private function optimizeImagesInHtml(string $content): string
    {
        // Add loading="lazy" to images that don't have it
        $content = preg_replace(
            '/<img(?![^>]*loading=)[^>]*>/i',
            '$0',
            $content
        );
        
        $content = preg_replace(
            '/<img([^>]*?)>/i',
            '<img$1 loading="lazy">',
            $content
        );
        
        // Add width and height attributes if missing (helps prevent layout shift)
        $content = preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']*)["\']([^>]*?)>/i',
            function($matches) {
                $fullMatch = $matches[0];
                $beforeSrc = $matches[1];
                $src = $matches[2];
                $afterSrc = $matches[3];
                
                // If width and height are not present, add default ones
                if (!str_contains($fullMatch, 'width=') && !str_contains($fullMatch, 'height=')) {
                    return '<img' . $beforeSrc . 'src="' . $src . '"' . $afterSrc . ' width="auto" height="auto">';
                }
                
                return $fullMatch;
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Add performance optimizations
     */
    private function addPerformanceOptimizations(string $content): string
    {
        // Add async loading to non-critical JavaScript
        $content = preg_replace(
            '/<script(?![^>]*async)(?![^>]*defer)([^>]*src=[^>]*?)>/i',
            '<script defer$1>',
            $content
        );
        
        // Add DNS prefetch for external domains
        $dnsPrefetch = '';
        if (str_contains($content, 'cdn.jsdelivr.net')) {
            $dnsPrefetch .= '<link rel="dns-prefetch" href="//cdn.jsdelivr.net">' . "\n";
        }
        if (str_contains($content, 'cdnjs.cloudflare.com')) {
            $dnsPrefetch .= '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">' . "\n";
        }
        
        if (!empty($dnsPrefetch)) {
            $content = str_replace('<head>', '<head>' . "\n" . $dnsPrefetch, $content);
        }
        
        return $content;
    }
}

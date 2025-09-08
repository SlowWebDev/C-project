<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\DeviceSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Update device session activity
        if (auth()->check()) {
            DeviceSession::registerOrUpdateDevice();
        }
        
        $response = $next($request);
        
        // Track specific admin activities after response
        if (auth()->check() && $request->is('admin/*') && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->trackActivity($request, $response);
        }
        
        return $response;
    }
    
    private function trackActivity(Request $request, Response $response)
    {
        // Only track successful responses
        if ($response->getStatusCode() >= 400) {
            return;
        }
        
        $routeName = $request->route()?->getName();
        $method = $request->method();
        $uri = $request->path();
        
        $action = $this->determineAction($routeName, $method, $uri);
        
        if ($action) {
            ActivityLog::logActivity(
                $action,
                null,
                null,
                null,
                $this->generateDescription($action, $routeName, $request)
            );
        }
    }
    
    private function determineAction(?string $routeName, string $method, string $uri): ?string
    {
        // Map route patterns to actions
        $patterns = [
            'admin.projects.store' => 'project_created',
            'admin.projects.update' => 'project_updated',
            'admin.projects.destroy' => 'project_deleted',
            'admin.media.store' => 'media_uploaded',
            'admin.media.update' => 'media_updated',
            'admin.media.destroy' => 'media_deleted',
            'admin.pages.home.update' => 'home_page_updated',
            'admin.pages.about.update' => 'about_page_updated',
            'admin.pages.contact.update' => 'contact_page_updated',
            'admin.settings.logo' => 'logo_updated',
            'admin.settings.careers.update' => 'careers_settings_updated',
            'admin.settings.footer' => 'footer_updated',
            'admin.careers.store' => 'job_created',
            'admin.careers.update' => 'job_updated',
            'admin.careers.destroy' => 'job_deleted',
            'admin.contacts.destroy' => 'contact_deleted',
            'admin.contacts.status' => 'contact_status_updated',
            'admin.seo.update' => 'seo_updated',
            'admin.seo.reset' => 'seo_reset',
        ];
        
        if ($routeName && isset($patterns[$routeName])) {
            return $patterns[$routeName];
        }
        
        // Fallback to generic patterns
        if (str_contains($uri, 'media') && $method === 'POST') return 'media_uploaded';
        if (str_contains($uri, 'media') && $method === 'DELETE') return 'media_deleted';
        if (str_contains($uri, 'projects') && $method === 'POST') return 'project_created';
        if (str_contains($uri, 'projects') && in_array($method, ['PUT', 'PATCH'])) return 'project_updated';
        if (str_contains($uri, 'projects') && $method === 'DELETE') return 'project_deleted';
        
        return null;
    }
    
    private function generateDescription(string $action, ?string $routeName, Request $request): string
    {
        $descriptions = [
            'project_created' => 'Created a new project',
            'project_updated' => 'Updated project information',
            'project_deleted' => 'Deleted a project',
            'media_uploaded' => 'Uploaded new media file',
            'media_updated' => 'Updated media information',
            'media_deleted' => 'Deleted media file',
            'home_page_updated' => 'Updated home page content',
            'about_page_updated' => 'Updated about page content',
            'contact_page_updated' => 'Updated contact page content',
            'logo_updated' => 'Updated website logo',
            'careers_settings_updated' => 'Updated careers page settings',
            'footer_updated' => 'Updated website footer',
            'job_created' => 'Created a new job posting',
            'job_updated' => 'Updated job posting',
            'job_deleted' => 'Deleted job posting',
            'contact_deleted' => 'Deleted contact message',
            'contact_status_updated' => 'Updated contact message status',
            'seo_updated' => 'Updated SEO settings and regenerated sitemap',
            'seo_reset' => 'Reset all SEO settings to defaults',
        ];
        
        return $descriptions[$action] ?? 'Performed admin action: ' . $action;
    }
}

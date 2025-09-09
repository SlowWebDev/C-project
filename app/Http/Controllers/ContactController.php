<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Setting;

/**
 * ContactController - Contact Form & Inquiries
 * 
 * Handles contact form submissions and project inquiries with rate limiting
 * 
 * @author SlowWebDev
 */
class ContactController extends Controller
{
    /**
     * Display contact page with settings
     */
    public function index()
    {
        // Load contact page settings from database
        $contactSettings = [
            'contact_title' => Setting::get('contact_information_title', 'Contact Information'),
            'address_title' => Setting::get('contact_address_title', 'Our Address'),
            'address_content' => Setting::get('contact_address_content', ''),
            'email_title' => Setting::get('contact_email_title', 'Email Us'),
            'email_content' => Setting::get('contact_email_content', ''),
            'phone_title' => Setting::get('contact_phone_title', 'Call Us'),
            'phone_content' => Setting::get('contact_phone_content', ''),
            'map_embed_url' => Setting::get('contact_map_embed_url', ''),
        ];

        return view('pages.contact', compact('contactSettings'));
    }

    /**
     * Process contact form submission
     * Includes rate limiting to prevent spam
     */
    public function store(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:general,project_inquiry',
        ]);

        // Rate limiting: Check submissions in last 10 minutes from same email or IP
        $cacheKey = 'contact_' . md5(strtolower($validated['email']) . $request->ip());
        
        if (cache()->has($cacheKey)) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait before sending another message.'
                ]);
            }
            
            return redirect()->back()->with('error', 'Please wait before sending another message.');
        }

        // Store in cache for 10 minutes to prevent duplicate submissions
        cache()->put($cacheKey, true, now()->addMinutes(10));

        // Save contact to database
        Contact::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? '',
            'type' => $validated['type'],
            'project_id' => $validated['project_id'] ?? null,
            'status' => 'new',
        ]);

        // Set success message based on inquiry type
        $successMessage = $validated['type'] === 'project_inquiry'
            ? 'Thank you for your project inquiry. We will contact you soon with details.'
            : 'Thank you for contacting us. We will get back to you soon.';

        // Handle AJAX vs regular form submission
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Handle project inquiry from individual project pages
     * Automatically sets project_id and creates appropriate message
     */
    public function storeProjectInquiry(Request $request, $projectId)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Add project information to the inquiry
        $validated['project_id'] = $projectId;
        $validated['type'] = 'project_inquiry';
        $project = Project::find($projectId);
        $validated['message'] = "Project inquiry for: " . ($project ? $project->title : 'Unknown Project');

        // Reuse the main store method with modified request
        $request->merge($validated);
        return $this->store($request);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Project;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
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

        // Store in cache for 10 minutes
        cache()->put($cacheKey, true, now()->addMinutes(10));

        // Save to database
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

        $successMessage = $validated['type'] === 'project_inquiry'
            ? 'Thank you for your project inquiry. We will contact you soon with details.'
            : 'Thank you for contacting us. We will get back to you soon.';

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    /**
     * Store project inquiry from project show page
     */
    public function storeProjectInquiry(Request $request, $projectId)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Add project info to validated data
        $validated['project_id'] = $projectId;
        $validated['type'] = 'project_inquiry';
        $project = Project::find($projectId);
        $validated['message'] = "Project inquiry for: " . ($project ? $project->title : 'Unknown Project');

        // Use the same store method but with modified request
        $request->merge($validated);
        return $this->store($request);
    }
}
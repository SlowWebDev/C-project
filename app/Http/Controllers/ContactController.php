<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Here you would normally save to database or send email
        // For now, we'll just return success

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us. We will get back to you soon.'
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for contacting us. We will get back to you soon.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Helpers\FileSecurityChecker;
use Illuminate\Support\Facades\Log;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = Job::where('is_active', true)->get();
        return view('pages.careers', compact('jobs'));
    }

    public function apply(Request $request)
    {
        try {
            Log::info('Career application received', $request->all());
            
            $validated = $request->validate([
                'job_id' => 'required|exists:jobs,id',
                'first_name' => 'required|string|min:2|max:50',
                'last_name' => 'required|string|min:2|max:50',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:15',
                'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            ], [
                'cv.mimes' => 'The CV must be a PDF file',
                'cv.max' => 'The CV file size must not exceed 5MB',
            ]);

            // Check if user already applied for this job
            $existingApplication = JobApplication::where('email', strtolower($validated['email']))
                ->where('job_id', $validated['job_id'])
                ->first();

            if ($existingApplication) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already applied for this position!'
                    ]);
                }
                
                return redirect()->back()
                    ->with('error', 'You have already applied for this position!');
            }

            // Simple file upload without security checks for testing
            $cvFile = $request->file('cv');
            $timestamp = time();
            $randomString = bin2hex(random_bytes(4));
            $secureFilename = "cv_{$timestamp}_{$randomString}.pdf";
            $cvPath = $cvFile->storeAs('cv_uploads', $secureFilename, 'public');

            $application = JobApplication::create([
                'job_id' => $validated['job_id'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'cv_path' => $cvPath,
                'status' => 'pending'
            ]);

            // Increment the applications count for the job
            $job = Job::find($validated['job_id']);
            if ($job) {
                $job->increment('applications_count');
            }

            Log::info('Application created successfully', ['id' => $application->id]);

            // Check if it's an Ajax request
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your application has been submitted successfully!'
                ]);
            }
            
            // Regular form submission - redirect with success message
            return redirect()->route('careers.index')
                ->with('success', 'Your application has been submitted successfully! We will contact you soon.');
            
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error('Career application submission error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            // Check if it's an Ajax request
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application failed: ' . $e->getMessage()
                ], 500);
            }
            
            // Regular form submission - redirect with detailed error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Application failed: ' . $e->getMessage());
        }
    }
}
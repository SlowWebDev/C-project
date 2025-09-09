<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

/**
 * Admin Contact Controller - Contact Submissions Management
 * 
 * Review, update status, and delete contact entries
 * 
 * @author SlowWebDev
 */
class ContactController extends Controller
{
    /**
     * List contact messages
     */
    public function index()
    {
        $contacts = Contact::with('project')->latest()->paginate(15);
        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Update contact message status (new/read/replied)
     */
    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $status = $request->input('status');
        
        if (in_array($status, ['new', 'read', 'replied'])) {
            $contact->update(['status' => $status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid status'
        ], 400);
    }
    
    /**
     * Delete a contact message permanently
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully');
    }
    
    /**
     * Mark all new contact messages as read
     */
    public function markAllAsRead()
    {
        $count = Contact::where('status', 'new')->update(['status' => 'read']);
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "$count messages marked as read"
        ]);
    }
}

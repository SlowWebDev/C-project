<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with('project')->latest()->paginate(15);
        return view('admin.contacts.index', compact('contacts'));
    }

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
    
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully');
    }
    
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

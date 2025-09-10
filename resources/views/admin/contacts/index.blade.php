{{--
    Admin Contacts Index - Customer Messages Dashboard
    
    Displays and manages all customer contact forms with status tracking,
    bulk operations, and direct email integration.
    
    Author: SlowWebDev
--}}

@extends('admin.layouts.admin')

@section('title', 'Contact Messages')
@section('description', 'Manage customer inquiries and messages')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">Contact Messages</h2>
        <p class="admin-page-description">Manage and respond to customer inquiries</p>
    </div>
    <div class="admin-flex-start admin-gap-4">
        <button onclick="AdminPanel.contact.markAllAsRead()" class="admin-btn-primary">
            <i class="fas fa-envelope-open"></i>
            <span>Mark All Read</span>
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-table-container">
        <table class="admin-main-table">
            <thead>
                <tr>
                    <th class="admin-table-header">Sender</th>
                    <th class="admin-table-header">Subject</th>
                    <th class="admin-table-header">Message</th>
                    <th class="admin-table-header">Status</th>
                    <th class="admin-table-header">Date</th>
                    <th class="admin-table-header">Actions</th>
                </tr>
            </thead>
            <tbody class="admin-table-body">
                @forelse($contacts as $contact)
                    <tr class="admin-table-row">
                        <td class="admin-table-cell">
                            <div class="admin-flex-start admin-gap-4">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($contact->first_name, 0, 1)) }}
                                </div>
                                <div class="admin-table-cell-content">
                                    <div class="admin-table-cell-title-text">{{ $contact->first_name }} {{ $contact->last_name }}</div>
                                    <div class="admin-table-cell-subtitle">{{ $contact->email }}</div>
                                    @if($contact->phone)
                                        <div class="admin-table-cell-subtitle">{{ $contact->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="admin-table-cell">
                            <div class="admin-table-cell-title-text">
                                {{ $contact->type === 'project_inquiry' ? 'Project Inquiry' : 'General Contact' }}
                            </div>
                            @if($contact->project)
                                <div class="admin-table-cell-subtitle">{{ $contact->project->title }}</div>
                            @endif
                        </td>
                        <td class="admin-table-cell">
                            <div class="max-w-xs">
                                @if(strlen($contact->message) > 15)
                                    <div>
                                        <p class="text-gray-300 text-sm mb-2">{{ substr($contact->message, 0, 15) }}...</p>
                                        <button onclick="showMessage({{ $contact->id }})" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition-colors">
                                            <i class="fas fa-eye mr-1"></i>
                                            Show more
                                        </button>
                                    </div>
                                @else
                                    <p class="text-gray-300 text-sm break-words">{{ $contact->message }}</p>
                                @endif
                            </div>
                            
                            @if(strlen($contact->message) > 15)
                            <!-- Enhanced Modal - Fixed positioning to avoid sidebar -->
                            <div id="modal_{{ $contact->id }}" class="fixed inset-0 z-40 hidden lg:right-64" onclick="hideMessage({{ $contact->id }})">
                                <!-- Backdrop -->
                                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>
                                
                                <!-- Modal Container -->
                                <div class="relative h-full flex items-center justify-center p-4">
                                    <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-600 w-full max-w-lg max-h-[85vh] overflow-hidden" onclick="event.stopPropagation()">
                                        <!-- Header -->
                                        <div class="bg-gray-700 px-4 py-3 border-b border-gray-600">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-envelope text-white text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-base font-semibold text-white">{{ $contact->first_name }} {{ $contact->last_name }}</h3>
                                                        <p class="text-xs text-gray-300">{{ $contact->email }}</p>
                                                    </div>
                                                </div>
                                                <button onclick="hideMessage({{ $contact->id }})" class="text-gray-400 hover:text-white hover:bg-gray-600 rounded p-1 transition-colors">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Message Content -->
                                        <div class="p-4 overflow-y-auto" style="max-height: 50vh;">
                                            <!-- Contact Info in one line -->
                                            <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-900 text-blue-200">
                                                    {{ $contact->type === 'project_inquiry' ? 'Project Inquiry' : 'General Contact' }}
                                                </span>
                                                @if($contact->project)
                                                    <span class="text-blue-400">{{ $contact->project->title }}</span>
                                                @endif
                                                @if($contact->phone)
                                                    <span class="text-gray-300">{{ $contact->phone }}</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Message -->
                                            <div class="bg-gray-700 rounded-lg p-3 border-l-3 border-blue-500">
                                                <p class="text-gray-200 text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $contact->message }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Footer -->
                                        <div class="bg-gray-700 px-4 py-3 border-t border-gray-600 flex justify-between items-center">
                                            <div class="text-xs text-gray-400">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ $contact->created_at->diffForHumans() }}
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="hideMessage({{ $contact->id }})" class="bg-gray-600 hover:bg-gray-500 text-white px-3 py-1 rounded text-sm transition-colors">
                                                    Close
                                                </button>
                                                <a href="mailto:{{ $contact->email }}?subject=Re: Your {{ $contact->type === 'project_inquiry' ? 'Project Inquiry' : 'Contact' }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors inline-flex items-center">
                                                    <i class="fas fa-reply mr-1 text-xs"></i>
                                                    Reply
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="admin-table-cell">
                            @if($contact->status === 'new')
                                <span class="admin-status-draft">New</span>
                            @elseif($contact->status === 'read')
                                <span class="admin-status-active">Read</span>
                            @else
                                <span class="admin-status-published">Replied</span>
                            @endif
                        </td>
                        <td class="admin-table-cell admin-text-muted">
                            {{ $contact->created_at->diffForHumans() }}
                        </td>
                        <td class="admin-table-cell">
                            <div class="admin-table-actions">
                                @if($contact->status === 'new')
                                    <button onclick="AdminPanel.contact.markAsRead({{ $contact->id }})" class="admin-btn-success" title="Mark as Read">
                                        <i class="fas fa-envelope-open"></i>
                                        <span class="admin-sr-only">Mark Read</span>
                                    </button>
                                @elseif($contact->status === 'read')
                                    <button onclick="AdminPanel.contact.markAsReplied({{ $contact->id }})" class="admin-btn-secondary" title="Mark as Replied">
                                        <i class="fas fa-reply"></i>
                                        <span class="admin-sr-only">Mark Replied</span>
                                    </button>
                                @else
                                    <button class="admin-btn-secondary" disabled title="Completed">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="admin-sr-only">Done</span>
                                    </button>
                                @endif
                                
                                <a href="mailto:{{ $contact->email }}?subject=Re: Your {{ $contact->type === 'project_inquiry' ? 'Project Inquiry' : 'Contact' }}" 
                                   class="admin-btn-primary" title="Reply via Email">
                                    <i class="fas fa-envelope"></i>
                                    <span class="admin-sr-only">Email</span>
                                </a>
                                
                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn-danger" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this contact?')">
                                        <i class="fas fa-trash"></i>
                                        <span class="admin-sr-only">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="admin-table-cell">
                            <div class="admin-empty-state">
                                <i class="fas fa-inbox"></i>
                                <p class="title">No Messages Found</p>
                                <p class="description">Customer messages will appear here when received</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($contacts->hasPages())
    <div class="admin-mt-6">
        {{ $contacts->links() }}
    </div>
@endif
@endsection

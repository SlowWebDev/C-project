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
                                <p class="admin-text-muted text-sm leading-5">{{ Str::limit($contact->message, 80) }}</p>
                                @if(strlen($contact->message) > 80)
                                    <button onclick="toggleMessage({{ $contact->id }})" class="text-blue-500 hover:text-blue-700 text-xs mt-1">
                                        <span id="toggle-text-{{ $contact->id }}">Show more</span>
                                    </button>
                                    <div id="full-message-{{ $contact->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded text-sm">
                                        {{ $contact->message }}
                                    </div>
                                @endif
                            </div>
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

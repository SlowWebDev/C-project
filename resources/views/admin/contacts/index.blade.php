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
        <button class="admin-btn-secondary">
            <i class="fas fa-filter"></i>
            <span>Filter</span>
        </button>
        <button class="admin-btn-primary">
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
                {{-- Example data structure - replace with actual data --}}
                @forelse([] as $message)
                    <tr class="admin-table-row">
                        <td class="admin-table-cell">
                            <div class="admin-flex-start admin-gap-4">
                                <div class="w-10 h-10 bg-blue-600 rounded-full  text-white font-semibold">
                                    {{ strtoupper(substr('John Doe', 0, 1)) }}
                                </div>
                                <div class="admin-table-cell-content">
                                    <div class="admin-table-cell-title-text">John Doe</div>
                                    <div class="admin-table-cell-subtitle">john@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="admin-table-cell">
                            <div class="admin-table-cell-title-text">Project Inquiry</div>
                        </td>
                        <td class="admin-table-cell">
                            <p class="admin-text-muted">{{ Str::limit('I am interested in discussing a potential project...', 50) }}</p>
                        </td>
                        <td class="admin-table-cell">
                            <span class="admin-status-draft">Unread</span>
                        </td>
                        <td class="admin-table-cell admin-text-muted">
                            {{ now()->diffForHumans() }}
                        </td>
                        <td class="admin-table-cell">
                            <div class="admin-table-actions">
                                <button class="admin-btn-primary" title="View Message">
                                    <i class="fas fa-eye"></i>
                                    <span class="admin-sr-only">View</span>
                                </button>
                                <button class="admin-btn-success" title="Mark as Read">
                                    <i class="fas fa-check"></i>
                                    <span class="admin-sr-only">Mark Read</span>
                                </button>
                                <button class="admin-btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                    <span class="admin-sr-only">Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="admin-table-cell ">
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

{{-- Pagination if needed --}}
{{-- @if($messages && $messages->count() > 0)
    <div class="admin-mt-6">
        {{ $messages->links() }}
    </div>
@endif --}}
@endsection

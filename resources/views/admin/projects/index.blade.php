{{--
    Admin Projects Index - Projects Management Dashboard
    
    Displays all projects in a data table with CRUD operations,
    filtering by category/status, and pagination.
    
    Author: SlowWebDev
--}}

@extends('admin.layouts.admin')

@section('title', 'Projects Management')
@section('description', 'Manage and view all projects.')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">Projects List</h2>
        <p class="admin-page-description">Manage and view all projects</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" 
       class="admin-btn-primary">
        <i class="fas fa-plus"></i>
        <span>Add New Project</span>
    </a>
</div>

{{-- Projects Data Table --}}
<div class="admin-card">
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="admin-table-header">Project Details</th>
                    <th class="admin-table-header">Category</th>
                    <th class="admin-table-header">Location</th>
                    <th class="admin-table-header">Status</th>
                    <th class="admin-table-header text-center">Actions</th>
                </tr>
            </thead>
        <tbody class="admin-table-body">
            @forelse($projects as $project)
                <tr class="admin-table-row">
                    <td class="admin-table-cell admin-table-cell-nowrap">
                        <div class="admin-table-cell-title">
                            <div class="flex-shrink-0">
                                <img class="admin-table-cell-image" 
                                     src="{{ Storage::url($project->image) }}" 
                                     alt="{{ $project->title }}">
                            </div>
                            <div class="admin-table-cell-content">
                                <div class="admin-table-cell-title-text">{{ $project->title }}</div>
                                <div class="admin-table-cell-subtitle">{{ Str::limit($project->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="admin-table-cell admin-table-cell-nowrap">
                        <span class="{{ $project->category === 'residential' ? 'admin-category-residential' : 'admin-category-commercial' }}">
                            {{ ucfirst($project->category) }}
                        </span>
                    </td>
                    <td class="admin-table-cell admin-table-cell-nowrap admin-text-muted">{{ $project->address }}</td>
                    <td class="admin-table-cell admin-table-cell-nowrap">
                        <span class="{{ $project->status === 'published' ? 'admin-status-published' : 'admin-status-draft' }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>

                    <td class="admin-table-cell admin-table-cell-nowrap">
                        <div class="admin-table-actions">
                            <a href="{{ route('admin.projects.show', $project) }}" 
                               class="admin-btn-primary">
                                <i class="fas fa-eye"></i>
                                <span class="admin-sr-only">View</span>
                            </a>
                            <a href="{{ route('admin.projects.edit', $project) }}" 
                               class="admin-btn-success">
                                <i class="fas fa-edit"></i>
                                <span class="admin-sr-only">Edit</span>
                            </a>
                            <form action="{{ route('admin.projects.destroy', $project) }}" 
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="admin-btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this project?')">
                                    <i class="fas fa-trash-alt"></i>
                                    <span class="admin-sr-only">Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="admin-table-cell">
                        <div class="admin-empty-state">
                            <i class="fas fa-folder-open admin-empty-icon"></i>
                            <p class="admin-empty-title">No Projects Found</p>
                            <p class="admin-empty-description">Start by adding your first project</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

@if($projects->count() > 0)
    <div class="admin-mt-6">
        {{ $projects->links() }}
    </div>
@endif
@endsection
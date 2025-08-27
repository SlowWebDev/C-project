@extends('admin.layouts.admin')

@section('title', 'Media Management')
@section('description', 'Manage and view all media.')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">Media Library</h2>
        <p class="admin-page-description">Manage and organize media content</p>
    </div>
    <a href="{{ route('admin.media.create') }}" 
       class="admin-btn-primary">
        <i class="fas fa-plus"></i>
        <span>Add New Media</span>
    </a>
</div>

<div class="admin-card">
    <div class="admin-table-container">
        <table class="admin-main-table">
            <thead>
                <tr>
                    <th class="admin-table-header">Media</th>
                    <th class="admin-table-header">Type</th>
                    <th class="admin-table-header">Status</th>
                    <th class="admin-table-header">Actions</th>
                </tr>
            </thead>
            <tbody class="admin-table-body">
                @forelse($mediaItems as $media)
                    <tr class="admin-table-row">
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <div class="admin-table-cell-title">
                                <div class="flex-shrink-0">
                                    <img class="admin-table-cell-image" 
                                         src="{{ Storage::url($media->image) }}" 
                                         alt="{{ $media->title }}">
                                </div>
                                <div class="admin-table-cell-content">
                                    <div class="admin-table-cell-title-text">{{ $media->title }}</div>
                                    <div class="admin-table-cell-subtitle">{{ Str::limit($media->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <span class="admin-category-commercial">
                                Media
                            </span>
                        </td>
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <span class="{{ $media->status === 'published' ? 'admin-status-published' : 'admin-status-draft' }}">
                                {{ ucfirst($media->status) }}
                            </span>
                        </td>
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <div class="admin-table-actions">
                                <a href="{{ route('admin.media.show', $media) }}" 
                                   class="admin-btn-primary">
                                    <i class="fas fa-eye"></i>
                                    <span class="admin-sr-only">View</span>
                                </a>
                                <a href="{{ route('admin.media.edit', $media) }}" 
                                   class="admin-btn-success">
                                    <i class="fas fa-edit"></i>
                                    <span class="admin-sr-only">Edit</span>
                                </a>
                                <form action="{{ route('admin.media.destroy', $media) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="admin-btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this media?')">
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="admin-sr-only">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="admin-table-cell ">
                            <div class="admin-empty-state">
                                <i class="fas fa-image"></i>
                                <p class="title">No Media Found</p>
                                <p class="description">Start by adding your first media item</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($mediaItems->count() > 0)
    <div class="admin-mt-6">
        {{ $mediaItems->links() }}
    </div>
@endif
@endsection
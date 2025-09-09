{{--
    Admin Careers Index - Job Postings & Applications Management
    
    Dual-purpose page managing both job positions and applications.
    Includes filtering, status updates, and application downloads.
    
    Author: SlowWebDev
--}}

@extends('admin.layouts.admin')

@section('title', 'Job Position Management')
@section('description', 'Manage job position')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">Job Positions</h2>
        <p class="admin-page-description">Manage available job positions and requirements</p>
    </div>
    @if($jobs->count() < 2)
    <a href="{{ route('admin.careers.create') }}" class="admin-btn-primary">
        <i class="fas fa-plus"></i>
        <span>Add New Position</span>
    </a>
    @else
    <p class="admin-text-muted">Maximum positions limit reached (2)</p>
    @endif
</div>

<div class="admin-card">
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="admin-table-header">Job Title</th>
                    <th class="admin-table-header">Requirements</th>
                    <th class="admin-table-header">Applications</th>
                    <th class="admin-table-header">Actions</th>
                </tr>
            </thead>
            <tbody class="admin-table-body">
                @forelse($jobs as $job)
                    <tr class="admin-table-row">
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <div class="admin-table-cell-title">
                                <div class="admin-table-cell-content">
                                    <div class="admin-table-cell-title-text">{{ $job->title }}</div>
                                    <div class="admin-table-cell-subtitle">Posted {{ $job->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="admin-table-cell">
                            <div class="space-y-1">
                                @foreach($job->requirements as $requirement)
                                    <div class="admin-table-cell-subtitle">• {{ $requirement }}</div>
                                @endforeach
                            </div>
                        </td>
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <span class="admin-category-commercial">
                                {{ $job->applications_count ?? 0 }} Applications
                            </span>
                        </td>
                        <td class="admin-table-cell admin-table-cell-nowrap">
                            <div class="admin-table-actions">
                                <a href="{{ route('admin.careers.edit', $job) }}" 
                                   class="admin-btn-success">
                                    <i class="fas fa-edit"></i>
                                    <span class="admin-sr-only">Edit</span>
                                </a>
                                <form action="{{ route('admin.careers.destroy', $job) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="admin-btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this job?')">
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="admin-sr-only">Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="admin-table-cell">
                            <div class="admin-empty-state">
                                <i class="fas fa-briefcase"></i>
                                <p class="title">No Jobs Found</p>
                                <p class="description">Start by adding your first job posting</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($jobs->count() > 0)
    <div class="mt-6">
        {{ $jobs->links() }}
    </div>
@endif

<!-- Applications Section -->
<div class="admin-mt-8">

    <!-- Filters -->
    <div class="mb-6">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-4">
                <div class="admin-filter-group">
                    <label class="admin-label">Filter by Job</label>
                    <select name="job" class="admin-select">
                        <option value="">All Jobs</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" {{ request('job') == $job->id ? 'selected' : '' }}>
                                {{ $job->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="admin-filter-group">
                    <label class="admin-label">Filter by Status</label>
                    <select name="status" class="admin-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="admin-filter-actions">
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-filter"></i>
                        <span>Apply Filter</span>
                    </button>
                    @if(request()->hasAny(['job', 'status']))
                        <a href="{{ route('admin.careers.index') }}" class="admin-btn-secondary">
                            <i class="fas fa-times"></i>
                            <span>Clear</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="admin-card">
        <div class="admin-table-container">
            <table class="admin-main-table">
                <thead>
                    <tr>
                        <th class="admin-table-header">
                            Applicant
                        </th>
                        <th class="admin-table-header">
                            Job Position
                        </th>
                        <th class="admin-table-header">
                            Contact
                        </th>
                        <th class="admin-table-header">
                            Status
                        </th>
                        <th class="admin-table-header">
                            Applied
                        </th>
                        <th class="admin-table-header">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="admin-table-body">
                    @forelse($applications as $application)
                        <tr class="admin-table-row">
                            <td class="admin-table-cell admin-table-cell-nowrap">
                                <div class="admin-table-cell-title">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full  text-white font-semibold">
                                            {{ strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="admin-table-cell-content">
                                        <div class="admin-table-cell-title-text">
                                            {{ $application->first_name }} {{ $application->last_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="admin-table-cell admin-table-cell-nowrap">
                                <div class="admin-table-cell-subtitle">
                                    {{ $application->job ? $application->job->title : 'Job Deleted' }}
                                </div>
                            </td>
                            <td class="admin-table-cell">
                                <div class="admin-table-cell-content">
                                    <div class="admin-table-cell-subtitle">{{ $application->email }}</div>
                                    @if($application->phone)
                                        <div class="admin-table-cell-subtitle">{{ $application->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="admin-table-cell admin-table-cell-nowrap">
                                <form action="{{ route('admin.careers.applications.status', $application) }}" method="POST" class="status-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="admin-select">
                                        <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                        <option value="contacted" {{ $application->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td class="admin-table-cell admin-table-cell-nowrap admin-text-muted">
                                {{ $application->created_at->diffForHumans() }}
                            </td>
                            <td class="admin-table-cell admin-table-cell-nowrap">
                                <div class="admin-table-actions">
                                    @if($application->cv_path)
                                        <a href="{{ route('admin.careers.applications.download-cv', $application) }}" 
                                           class="admin-btn-primary" title="Download CV">
                                            <i class="fas fa-download"></i>
                                            <span class="admin-sr-only">Download CV</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="admin-table-cell ">
                                <div class="admin-empty-state">
                                    <i class="fas fa-file-alt"></i>
                                    <p class="title">No Applications Found</p>
                                    <p class="description">Applications will appear here once submitted</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($applications->count() > 0)
        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection

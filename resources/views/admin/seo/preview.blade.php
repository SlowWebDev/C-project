@extends('admin.layouts.admin')

@section('title', 'SEO Preview - ' . $pages[$page])
@section('description', 'Preview how this page will appear in search results and social media.')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold inline-block mb-4">{{ $pages[$page] }}</div>
                    <h1 class="text-3xl font-bold text-white mb-2">SEO Preview</h1>
                    <p class="text-gray-400">See how this page will appear in search results and social media</p>
                </div>
                <a href="{{ route('admin.seo.unified') }}" class="admin-btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to SEO Manager
                </a>
            </div>
        </div>

        <!-- Google Search Preview -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Google Search Results
            </h2>
            
            <div class="font-arial max-w-xl">
                <a href="#" class="text-blue-600 text-xl leading-snug mb-1 block hover:underline no-underline">{{ $pageData['meta_title'] ?: $pageData['title'] ?: 'Page Title' }}</a>
                <div class="text-green-700 text-sm mb-1">{{ $pageData['canonical'] ?: url($page === 'home' ? '/' : '/' . $page) }}</div>
                <div class="text-gray-600 text-sm leading-relaxed">
                    {{ $pageData['meta_description'] ?: 'No meta description available. Add a compelling description to improve your search engine visibility.' }}
                </div>
            </div>
        </div>

        <!-- Social Media Preview -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-share-alt text-blue-600 w-6 h-6 mr-3"></i>
                Social Media Share Preview
            </h2>
            
            <div class="border border-gray-300 rounded-lg max-w-lg bg-white overflow-hidden">
                <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-500 text-sm">
                    @if($pageData['og_image'])
                        <img src="{{ Storage::url($pageData['og_image']) }}" alt="OG Image" class="w-full h-full object-cover">
                    @else
                        <span>No image uploaded</span>
                    @endif
                </div>
                <div class="p-3 border-t border-gray-300">
                    <div class="text-xs text-gray-500 uppercase mb-0.5">{{ parse_url($pageData['canonical'] ?: url('/'), PHP_URL_HOST) }}</div>
                    <div class="text-base font-semibold text-gray-900 leading-tight mb-0.5">{{ $pageData['og_title'] ?: $pageData['meta_title'] ?: $pageData['title'] ?: 'Page Title' }}</div>
                    <div class="text-sm text-gray-600 leading-snug">
                        {{ $pageData['og_description'] ?: $pageData['meta_description'] ?: 'No description available for social media sharing.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Recommendations -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                SEO Recommendations
            </h2>

            <div class="space-y-4">
                @php
                $recommendations = [];
                
                if (!$pageData['meta_title']) {
                    $recommendations[] = ['type' => 'warning', 'text' => 'Meta title is missing. Add a compelling title (50-60 characters).'];
                } elseif (strlen($pageData['meta_title']) > 60) {
                    $recommendations[] = ['type' => 'warning', 'text' => 'Meta title is too long (' . strlen($pageData['meta_title']) . ' characters). Keep it under 60 characters.'];
                } elseif (strlen($pageData['meta_title']) < 30) {
                    $recommendations[] = ['type' => 'info', 'text' => 'Meta title is quite short (' . strlen($pageData['meta_title']) . ' characters). Consider expanding it.'];
                }
                
                if (!$pageData['meta_description']) {
                    $recommendations[] = ['type' => 'warning', 'text' => 'Meta description is missing. Add a description (150-160 characters).'];
                } elseif (strlen($pageData['meta_description']) > 160) {
                    $recommendations[] = ['type' => 'warning', 'text' => 'Meta description is too long (' . strlen($pageData['meta_description']) . ' characters). Keep it under 160 characters.'];
                } elseif (strlen($pageData['meta_description']) < 120) {
                    $recommendations[] = ['type' => 'info', 'text' => 'Meta description could be longer (' . strlen($pageData['meta_description']) . ' characters). Consider expanding to 150-160 characters.'];
                }
                
                if (!$pageData['og_image']) {
                    $recommendations[] = ['type' => 'warning', 'text' => 'Open Graph image is missing. Add an image (1200x630px) for better social media sharing.'];
                }
                
                if (!$pageData['canonical']) {
                    $recommendations[] = ['type' => 'info', 'text' => 'Canonical URL is not set. Consider adding it to prevent duplicate content issues.'];
                }
                
                if (!$pageData['active']) {
                    $recommendations[] = ['type' => 'error', 'text' => 'This page is marked as inactive. It won\'t be included in sitemaps.'];
                }
                
                if (!$pageData['indexable']) {
                    $recommendations[] = ['type' => 'warning', 'text' => 'This page is marked as non-indexable. Search engines won\'t index it.'];
                }
                
                if (empty($recommendations)) {
                    $recommendations[] = ['type' => 'success', 'text' => 'Great! Your SEO settings look good for this page.'];
                }
                @endphp

                @foreach($recommendations as $rec)
                    <div class="flex items-start space-x-3 p-3 rounded-lg 
                        @if($rec['type'] === 'error') bg-red-50 border border-red-200
                        @elseif($rec['type'] === 'warning') bg-yellow-50 border border-yellow-200  
                        @elseif($rec['type'] === 'info') bg-blue-50 border border-blue-200
                        @else bg-green-50 border border-green-200 @endif">
                        
                        <span class="text-lg">
                            @if($rec['type'] === 'error') ERROR
                            @elseif($rec['type'] === 'warning') WARNING
                            @elseif($rec['type'] === 'info') INFO
                            @else SUCCESS @endif
                        </span>
                        
                        <span class="
                            @if($rec['type'] === 'error') text-red-800
                            @elseif($rec['type'] === 'warning') text-yellow-800
                            @elseif($rec['type'] === 'info') text-blue-800
                            @else text-green-800 @endif">
                            {{ $rec['text'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Technical Details -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Technical Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Page Settings</h3>
                    <ul class="space-y-2 text-sm">
                        <li><strong>Status:</strong> 
                            @if($pageData['active'])
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </li>
                        <li><strong>Indexable:</strong> 
                            @if($pageData['indexable'])
                                <span class="text-green-600">Yes</span>
                            @else
                                <span class="text-yellow-600">No</span>
                            @endif
                        </li>
                        <li><strong>Priority:</strong> {{ $pageData['priority'] ?? 'N/A' }}/10</li>
                        <li><strong>Change Frequency:</strong> {{ ucfirst($pageData['frequency'] ?? 'monthly') }}</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Content Length</h3>
                    <ul class="space-y-2 text-sm">
                        <li><strong>Page Title:</strong> {{ strlen($pageData['title'] ?? '') }} characters</li>
                        <li><strong>Meta Title:</strong> {{ strlen($pageData['meta_title'] ?? '') }} characters</li>
                        <li><strong>Meta Description:</strong> {{ strlen($pageData['meta_description'] ?? '') }} characters</li>
                        <li><strong>Meta Keywords:</strong> {{ strlen($pageData['meta_keywords'] ?? '') }} characters</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

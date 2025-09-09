{{--
    Main Application Layout - Frontend Structure
    
    Provides the complete frontend layout with dynamic SEO meta tags,
    navigation, footer, and optimized loading for all public pages.
    
    Author: SlowWebDev
--}}

@php
$currentRoute = request()->route()->getName();
$pageKey = match($currentRoute) {
    'home' => 'home',
    'about' => 'about',
    'contact.index' => 'contact',
    'projects.index' => 'projects',
    'media.index' => 'media',
    'careers.index' => 'careers',
    default => null
};

$seoSettings = \App\Models\UnifiedSeo::getInstance();
$seoData = $pageKey ? $seoSettings->getPageData($pageKey) : null;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if($seoData && $seoData['active'] && $seoData['meta_title'])
        <title>{{ $seoData['meta_title'] }}</title>
        @if($seoData['meta_description'])
            <meta name="description" content="{{ $seoData['meta_description'] }}">
        @endif
        @if($seoData['meta_keywords'])
            <meta name="keywords" content="{{ $seoData['meta_keywords'] }}">
        @endif
        @if($seoData['canonical'])
            <link rel="canonical" href="{{ $seoData['canonical'] }}">
        @endif
        @if(!$seoData['indexable'])
            <meta name="robots" content="noindex, nofollow">
        @else
            <meta name="robots" content="index, follow">
        @endif
    @else
        <title>@yield('title', $seoSettings->site_name ?? 'C-Project')</title>
        <meta name="description" content="@yield('description', 'Professional construction and development services')">
        <meta name="robots" content="index, follow">
    @endif
    
    {{-- Favicon & Touch Icons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    @if($seoData && $seoData['active'] && ($seoData['og_title'] || $seoData['meta_title']))
        {{-- Open Graph / Social Media Meta Tags --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:title" content="{{ $seoData['og_title'] ?: $seoData['meta_title'] ?: $seoData['title'] }}">
        @if($seoData['og_description'] || $seoData['meta_description'])
            <meta property="og:description" content="{{ $seoData['og_description'] ?: $seoData['meta_description'] }}">
        @endif
        @if($seoData['og_image'])
            <meta property="og:image" content="{{ Storage::url($seoData['og_image']) }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta property="og:image:alt" content="{{ $seoData['meta_title'] ?? $seoData['title'] }}">
        @elseif($seoSettings->default_og_image)
            <meta property="og:image" content="{{ Storage::url($seoSettings->default_og_image) }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta property="og:image:alt" content="{{ $seoSettings->site_name }}">
        @endif
        <meta property="og:site_name" content="{{ $seoSettings->site_name ?? config('app.name') }}">
        <meta property="og:locale" content="en_US">
        
        
    @endif
    
    {{-- Performance Optimization - Resource Hints --}}
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    {{-- Optimized Font Loading - Inter Font Family --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"></noscript>
    
    {{-- SEO Enhancement --}}
    <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}">
    
    {{-- Compiled Assets - Vite Build --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/main.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased" style="font-display: swap;">    
    {{-- Site Navigation --}}
    @include('partials.navigation')
    
    {{-- Page Content Area --}}
    <main>
        @yield('content')
    </main>
    
    {{-- Site Footer --}}
    @include('partials.footer')

    @stack('scripts')
</body>
</html>
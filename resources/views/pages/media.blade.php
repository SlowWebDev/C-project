{{--
    Media Page - News and Events Gallery
    
    Displays media banner and gallery of published media items 
    including news, events, and company updates.
    
    Author: SlowWebDev
--}}

@extends('layouts.app')

@section('title', 'Media Center')
@section('description', 'Explore our media gallery showcasing our latest events')

@section('content')
<x-media.banner />
<x-media.gallery :mediaItems="$mediaItems" />
@endsection

@extends('layouts.app')

@section('title', 'Home - Slow')

@section('content')
    <x-home.hero-section/>
    <x-home.why-choose-us />
    <x-home.ceo-message />
    <x-home.our-projects :projects="$projects" />
    <x-home.our-partners />
    <x-home.media-new :mediaItems="$mediaItems" />
@endsection
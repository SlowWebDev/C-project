@extends('layouts.app')

@section('title', 'Home - Slow')

@section('content')
    <x-home.hero-section :settings="$homeSettings" />
    <x-home.why-choose-us :settings="$homeSettings" />
    <x-home.ceo-message :settings="$homeSettings" />
    <x-home.our-projects :projects="$projects" />
    <x-home.our-partners />
    <x-home.media-new :mediaItems="$mediaItems" />
@endsection

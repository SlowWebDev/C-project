@extends('layouts.app')

@section('title', 'Media Center')
@section('description', 'Explore our media gallery showcasing our latest events')

@section('content')
<x-media.banner />
<x-media.gallery :mediaItems="$mediaItems" />
@endsection

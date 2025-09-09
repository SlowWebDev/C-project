{{--
    Projects Page - Portfolio Showcase
    
    Displays project banner and grid of all published projects 
    with category filtering and detailed views.
    
    Author: SlowWebDev
--}}

@extends('layouts.app')

@section('title', 'Our Projects ')
@section('description', 'Explore portfolio of residential and commercial projects')

@section('content')

<x-projects.banner/>
<x-projects.sections :projects="$projects" />
@endsection
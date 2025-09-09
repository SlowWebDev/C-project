{{--
    About Page - Company Information
    
    Displays company banner and detailed information about 
    the organization, history, and values.
    
    Author: SlowWebDev
--}}

@extends('layouts.app')

@section('title', 'About Us ')

@section('description', 'Learn about Us.')

@section('content')

<x-about.banner />
<x-about.about-us :settings="$aboutSettings" />

@endsection

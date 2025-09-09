{{--
    Careers Page - Job Opportunities
    
    Displays career banner and list of active job positions 
    with application forms and company benefits.
    
    Author: SlowWebDev
--}}

@extends('layouts.app')

@section('title', 'Careers')
@section('description', 'Join our team page')

@section('content')

<x-careers.banner />
<x-careers.positions :jobs="$jobs" />

@endsection

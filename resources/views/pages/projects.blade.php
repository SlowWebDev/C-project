@extends('layouts.app')

@section('title', 'Our Projects ')
@section('description', 'Explore portfolio of residential and commercial projects')

@section('content')

<x-projects.banner/>
<x-projects.sections :projects="$projects" />
@endsection
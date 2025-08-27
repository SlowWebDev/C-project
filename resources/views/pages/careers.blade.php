@extends('layouts.app')

@section('title', 'Careers')
@section('description', 'Join our team page')

@section('content')

<x-careers.banner />
<x-careers.positions :jobs="$jobs" />

@endsection

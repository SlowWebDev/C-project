@extends('layouts.app')

@section('title', 'Contact Us')
@section('description', 'Get in touch with us')

@section('content')

<x-contact.banner />
<x-contact.contact-us :settings="$contactSettings" />

@endsection
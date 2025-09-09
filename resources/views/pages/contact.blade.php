{{--
    Contact Page - Customer Communication
    
    Provides contact forms, company information, and 
    multiple ways to reach the organization.
    
    Author: SlowWebDev
--}}

@extends('layouts.app')

@section('title', 'Contact Us')
@section('description', 'Get in touch with us')

@section('content')

<x-contact.banner />
<x-contact.contact-us :settings="$contactSettings" />

@endsection
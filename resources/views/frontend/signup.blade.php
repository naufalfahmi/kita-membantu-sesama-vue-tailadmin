@extends('layouts.frontend')

@php
    $page = 'signup';
@endphp

@section('title', 'Sign Up - ' . config('app.name', 'KMS'))

@section('content')
    @include('frontend.signup_content')
@endsection

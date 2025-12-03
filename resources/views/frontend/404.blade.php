@extends('layouts.frontend')

@php
    $page = '404';
@endphp

@section('title', '404 - Page Not Found - ' . config('app.name', 'KMS'))

@section('content')
    @include('frontend.404_content')
@endsection

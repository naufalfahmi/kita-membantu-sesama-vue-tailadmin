@extends('layouts.frontend')

@php
    $page = 'blog-grid';
@endphp

@section('title', 'Blog Grid - ' . config('app.name', 'KMS'))

@section('content')
    @include('frontend.blog-grid_content')
@endsection

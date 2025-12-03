@extends('layouts.frontend')

@php
    $page = 'blog-single';
@endphp

@section('title', 'Blog Single - ' . config('app.name', 'KMS'))

@section('content')
    @include('frontend.blog-single_content')
@endsection

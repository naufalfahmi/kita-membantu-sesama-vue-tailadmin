@extends('layouts.frontend')

@php
    $page = 'galeri';
@endphp

@section('title', config('app.name', 'KMS') . ' - Galeri')

@section('content')
    @include('frontend.galeri_content')
@endsection

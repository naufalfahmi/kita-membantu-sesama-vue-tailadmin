@extends('layouts.frontend')

@php
    $page = 'kontak';
@endphp

@section('title', config('app.name', 'KMS') . ' - Kontak')

@section('content')
    @include('frontend.kontak_content')
@endsection

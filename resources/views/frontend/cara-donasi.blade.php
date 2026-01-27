@extends('layouts.frontend')

@php
    $page = 'cara-donasi';
@endphp

@section('title', config('app.name', 'KMS') . ' - Cara Berdonasi')

@section('content')
    @include('frontend.cara-donasi_content')
@endsection

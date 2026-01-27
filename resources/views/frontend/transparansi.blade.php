@extends('layouts.frontend')

@php
    $page = 'transparansi';
@endphp

@section('title', config('app.name', 'KMS') . ' - Transparansi')

@section('content')
    @include('frontend.transparansi_content')
@endsection

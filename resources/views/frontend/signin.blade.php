@extends('layouts.frontend')

@php
    $page = 'signin';
@endphp

@section('title', 'Sign In - ' . config('app.name', 'KMS'))

@section('content')
    @include('frontend.signin_content')
@endsection

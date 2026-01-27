@extends('layouts.frontend')

@php
    $page = 'program';
@endphp

@section('title', config('app.name', 'KMS') . ' - Program')

@section('content')
    @include('frontend.program_content')
@endsection

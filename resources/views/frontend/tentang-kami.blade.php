@extends('layouts.frontend')

@php
    $page = 'tentang-kami';
@endphp

@section('title', config('app.name', 'KMS') . ' - Tentang Kami')

@section('content')
    @include('frontend.tentang-kami_content')
@endsection

<?php /** simple wrapper for program single view */ ?>
@extends('layouts.frontend')

@php
    $page = 'program-single';
@endphp

@section('title', 'Program - ' . config('app.name', 'KMS'))

@section('content')
    @include('frontend.program-single_content')
@endsection

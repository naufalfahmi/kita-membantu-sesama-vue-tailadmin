@extends('layouts.frontend')

@php
    $page = 'home';
@endphp

@section('title', config('app.name', 'KMS') . ' - Home')

@section('content')
    @include('frontend.index_content')
@endsection

@push('scripts')
<script>
  // Homepage scripts
</script>
@endpush

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name', 'KMS'))</title>
  <link rel="icon" href="{{ asset('frontend/favicon.ico') }}">
  <link href="{{ asset('frontend/style.css') }}" rel="stylesheet">
</head>

<body x-data="{ page: '{{ $page ?? 'home' }}', 'darkMode': true, 'stickyMenu': false, 'navigationOpen': false, 'scrollTop': false }"
  x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
  :class="{'b eh': darkMode === true}">
  
  @include('partials.frontend.header')

  <main>
    @yield('content')
  </main>

  @include('partials.frontend.footer')
  @include('partials.frontend.back-to-top')

  @stack('scripts')
  <script defer src="{{ asset('frontend/bundle.js') }}"></script>
</body>

</html>




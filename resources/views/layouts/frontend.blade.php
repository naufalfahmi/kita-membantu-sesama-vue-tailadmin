<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name', 'KMS'))</title>
  <!-- Prefer scalable SVG favicon, fallback to legacy ICO (versioned to bust cache) -->
  <link rel="icon" href="{{ asset('favicon.svg') }}?v={{ filemtime(public_path('favicon.svg')) }}" type="image/svg+xml">
  <link rel="alternate icon" href="{{ asset('frontend/favicon.ico') }}?v={{ filemtime(public_path('favicon.svg')) }}">
  <link href="{{ asset('frontend/style.css') }}" rel="stylesheet">
</head>

<body x-data="{
          page: '{{ $page ?? 'home' }}',
          darkMode: true,
          stickyMenu: false,
          navigationOpen: false,
          scrollTop: false,
          activeSection: window.location.hash ? window.location.hash.replace('#','') : '{{ $page ?? 'home' }}',
          setActiveSection(section) {
            this.activeSection = section || 'home';
          },
          init() {
            this.darkMode = JSON.parse(localStorage.getItem('darkMode'));
            this.$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));

            const sectionIds = ['home','tentang-kami','program','cara-donasi','galeri','transparansi','kontak'];
            const updateSection = (section) => this.setActiveSection(section);
            const initialSection = window.location.hash ? window.location.hash.replace('#','') : null;
            if (initialSection) { updateSection(initialSection); }
            window.addEventListener('hashchange', () => updateSection(window.location.hash.replace('#','') || 'home'));

            const queue = window.queueMicrotask || function(cb){ setTimeout(cb, 0); };
            if ('IntersectionObserver' in window) {
              const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                  if (entry.isIntersecting && entry.target.id) {
                    updateSection(entry.target.id);
                  }
                });
              }, { threshold: 0.35 });
              queue(() => {
                sectionIds.forEach(id => {
                  const el = document.getElementById(id);
                  if (el) { observer.observe(el); }
                });
              });
              window.addEventListener('beforeunload', () => observer.disconnect(), { once: true });
            }
          }
        }"
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





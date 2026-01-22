<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php
    $metaTitle = trim(View::hasSection('title') ? trim(View::getSection('title')) : (config('app.name', 'KMS')));
    $metaDescription = $metaDescription ?? ($landingProfile->meta_description ?? null) ?? 'Kita Membantu Sesama - lembaga kemanusiaan.';
    $metaImage = $metaImage ?? ($landingProfile->hero_image ?? asset('frontend/images/og-default.png'));
    $canonical = $canonical ?? url()->current();
  @endphp
  <title>@yield('title', $metaTitle)</title>
  <meta name="description" content="{{ Str::limit(strip_tags($metaDescription), 160) }}">
  <link rel="canonical" href="{{ $canonical }}" />

  <!-- Open Graph -->
  <meta property="og:site_name" content="{{ config('app.name', 'KMS') }}" />
  <meta property="og:title" content="@yield('title', $metaTitle)" />
  <meta property="og:description" content="{{ Str::limit(strip_tags($metaDescription), 200) }}" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ $canonical }}" />
  <meta property="og:image" content="{{ $metaImage }}" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="@yield('title', $metaTitle)" />
  <meta name="twitter:description" content="{{ Str::limit(strip_tags($metaDescription), 200) }}" />
  <meta name="twitter:image" content="{{ $metaImage }}" />

  <!-- JSON-LD Organization + WebSite -->
  <script type="application/ld+json">
  @php
    echo json_encode([
      '@context' => 'https://schema.org',
      '@graph' => [
        [
          '@type' => 'Organization',
          'name' => config('app.name', 'KMS'),
          'url' => url('/'),
          'logo' => ($landingProfile->logo ?? asset('frontend/images/logo.png')),
        ],
        [
          '@type' => 'WebSite',
          'url' => url('/'),
          'name' => config('app.name', 'KMS'),
          'description' => Str::limit(strip_tags($metaDescription), 200),
        ]
      ]
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  @endphp
  </script>
  <!-- Prefer scalable SVG favicon, fallback to legacy ICO (versioned to bust cache) -->
  <link rel="icon" href="{{ asset('favicon.svg') }}?v={{ filemtime(public_path('favicon.svg')) }}" type="image/svg+xml">
  <link rel="alternate icon" href="{{ asset('frontend/favicon.ico') }}?v={{ filemtime(public_path('favicon.svg')) }}">
  <link href="{{ asset('frontend/style.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css">
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
  @stack('scripts')
  <script defer src="{{ asset('frontend/bundle.js') }}"></script>
</body>

</html>





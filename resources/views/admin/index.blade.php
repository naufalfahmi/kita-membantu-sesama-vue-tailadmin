<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'KMS') }} - Admin Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <base href="{{ url('/') }}/">
    
        <!-- Use the prebuilt admin assets (served from /admin/assets) to ensure
            dynamic imports resolve from the same base path and avoid 404/MIME issues. -->
        <script type="module" src="/admin/assets/admin-DcHNUzMI.js" crossorigin></script>
        <link rel="stylesheet" href="/admin/assets/admin.css" crossorigin>
</head>
<body class="dark:bg-gray-900">
    <div id="app"></div>
</body>
</html>


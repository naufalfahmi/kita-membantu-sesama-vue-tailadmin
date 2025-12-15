<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'KMS') }} - Admin Dashboard</title>
    <!-- SVG favicon -->
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('frontend/favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    
    <style>
        #app-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: system-ui, sans-serif;
            color: #666;
        }
        #app-loading .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 12px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    @vite(['resources/js/admin.js'])
</head>
<body class="dark:bg-gray-900">
    <div id="app">
        <div id="app-loading">
            <div class="spinner"></div>
            <span>Loading...</span>
        </div>
    </div>
    <script>
        // Debug: log when Vue mounts
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.message, e.filename, e.lineno);
            document.getElementById('app-loading').innerHTML = 
                '<div style="color: red; text-align: center;">' +
                '<p>Error loading application</p>' +
                '<p style="font-size: 12px;">' + e.message + '</p>' +
                '</div>';
        });
    </script>
</body>
</html>


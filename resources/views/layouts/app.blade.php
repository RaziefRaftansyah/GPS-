<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if ($hasViteBuild)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: Figtree, sans-serif; background: #f3f4f6; color: #111827; }
                .min-h-screen { min-height: 100vh; }
                .bg-gray-100 { background: #f3f4f6; }
                .bg-white { background: #fff; }
                .shadow { box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
                .shadow-sm { box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06); }
                .rounded-lg { border-radius: 0.75rem; }
                .rounded-md { border-radius: 0.5rem; }
                .border-gray-200 { border-color: #e5e7eb; }
                .max-w-7xl { max-width: 80rem; }
                .mx-auto { margin-left: auto; margin-right: auto; }
                .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
                .px-4 { padding-left: 1rem; padding-right: 1rem; }
                .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
                .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; }
            </style>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>

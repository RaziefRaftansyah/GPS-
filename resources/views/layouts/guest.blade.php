<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @if ($hasViteBuild)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <link rel="stylesheet" href="{{ asset('css/layouts/guest-layout.css') }}">
        @stack('styles')
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <a href="{{ route('tracker.index') }}" class="guest-back-link">
            <span class="guest-back-icon">&larr;</span>
            <span>Kembali</span>
        </a>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            @if (request()->routeIs('login') || request()->routeIs('register'))
                <div class="w-full guest-auth-slot">
                    {{ $slot }}
                </div>
            @else
                <div>
                    <a href="{{ route('tracker.index') }}">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            @endif
        </div>

        @stack('scripts')
    </body>
</html>

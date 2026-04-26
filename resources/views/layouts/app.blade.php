<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700&display=swap" rel="stylesheet" />

        @if ($hasViteBuild)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <link rel="stylesheet" href="{{ asset('css/layouts/app-layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layouts/navigation.css') }}">
        @stack('styles')
    </head>
    <body>
        <div class="dashboard-shell">
            @include('layouts.navigation')

            <div class="dashboard-main">
                <div class="dashboard-topbar">
                    <div class="dashboard-topbar-left">
                        <button type="button" class="dashboard-menu-toggle" data-sidebar-toggle aria-label="Buka navigasi" aria-expanded="false">
                            <span></span>
                        </button>

                        <a href="{{ route('tracker.index') }}" class="dashboard-back">
                            <span>&larr;</span>
                            <span>Kembali ke Peta</span>
                        </a>
                    </div>

                    <div class="dashboard-topbar-right">
                        <div class="dashboard-user-chip">
                            <div class="dashboard-user-meta">
                                <strong>{{ Auth::user()->name }}</strong>
                                <span>{{ Auth::user()->isDriver() ? 'Driver aktif' : 'Owner aktif' }}</span>
                            </div>
                            <div class="dashboard-user-avatar">
                                @if (! blank(Auth::user()->profile_photo_url))
                                    <img
                                        src="{{ Auth::user()->profile_photo_url }}"
                                        alt="Avatar {{ Auth::user()->name }}"
                                        class="dashboard-user-avatar-image"
                                    >
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @isset($header)
                    <header class="dashboard-header-card">
                        {{ $header }}
                    </header>
                @endisset

                <main class="dashboard-content">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <script src="{{ asset('js/layouts/app-layout.js') }}"></script>
        @stack('scripts')
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700&display=swap" rel="stylesheet" />

        @if ($hasViteBuild)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                color-scheme: light;
                --app-bg: #f3f6fb;
                --sidebar-bg: #111827;
                --sidebar-soft: #1f2937;
                --sidebar-border: rgba(255, 255, 255, 0.08);
                --text-main: #0f172a;
                --text-soft: #64748b;
                --panel: #ffffff;
                --panel-border: #e2e8f0;
                --accent: #2563eb;
                --accent-soft: rgba(37, 99, 235, 0.12);
                --success: #16a34a;
                --danger: #dc2626;
                --shadow-lg: 0 20px 45px rgba(15, 23, 42, 0.08);
                --shadow-sm: 0 10px 25px rgba(15, 23, 42, 0.05);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Figtree, sans-serif;
                background: var(--app-bg);
                color: var(--text-main);
            }

            .dashboard-shell {
                min-height: 100vh;
                display: grid;
                grid-template-columns: 280px minmax(0, 1fr);
            }

            .dashboard-main {
                min-width: 0;
                display: flex;
                flex-direction: column;
            }

            .dashboard-topbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                padding: 28px 32px 0;
            }

            .dashboard-header-card {
                width: 100%;
                padding: 24px 28px;
                border-bottom: 1px solid var(--panel-border);
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(14px);
            }

            .dashboard-content {
                flex: 1;
                padding: 24px 32px 32px;
            }

            .dashboard-back {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 10px 16px;
                border-radius: 12px;
                background: #fff;
                border: 1px solid var(--panel-border);
                color: var(--text-main);
                text-decoration: none;
                font-weight: 600;
                box-shadow: var(--shadow-sm);
            }

            @media (max-width: 1024px) {
                .dashboard-shell {
                    grid-template-columns: 1fr;
                }

                .dashboard-topbar,
                .dashboard-content {
                    padding-left: 18px;
                    padding-right: 18px;
                }

                .dashboard-header-card {
                    padding-left: 18px;
                    padding-right: 18px;
                }
            }
        </style>
    </head>
    <body>
        <div class="dashboard-shell">
            @include('layouts.navigation')

            <div class="dashboard-main">
                <div class="dashboard-topbar">
                    <a href="{{ route('tracker.index') }}" class="dashboard-back">
                        <span>←</span>
                        <span>Kembali ke Peta</span>
                    </a>
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
    </body>
</html>

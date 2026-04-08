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
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    font-family: Figtree, sans-serif;
                    color: #1f2937;
                    background:
                        radial-gradient(circle at top left, rgba(181, 106, 59, 0.2), transparent 25%),
                        linear-gradient(145deg, #f2e6d5 0%, #f7f0e3 50%, #efe6d7 100%);
                }
                a { color: inherit; }
                .min-h-screen { min-height: 100vh; }
                .flex { display: flex; }
                .flex-col { flex-direction: column; }
                .items-center { align-items: center; }
                .justify-center { justify-content: center; }
                .pt-6 { padding-top: 1.5rem; }
                .sm\:pt-0 { padding-top: 0; }
                .w-full { width: 100%; }
                .sm\:max-w-md { max-width: 28rem; }
                .mt-6 { margin-top: 1.5rem; }
                .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
                .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
                .bg-white { background: rgba(255, 251, 245, 0.94); }
                .shadow-md { box-shadow: 0 20px 50px rgba(59, 36, 24, 0.12); }
                .overflow-hidden { overflow: hidden; }
                .sm\:rounded-lg { border-radius: 1rem; }
                .rounded-md { border-radius: 0.5rem; }
                input {
                    width: 100%;
                    padding: 0.75rem 0.9rem;
                    border-radius: 0.75rem;
                    border: 1px solid #d6c4af;
                    background: rgba(255,255,255,0.92);
                }
                label { font-size: 0.95rem; font-weight: 600; color: #5b3a29; }
                button {
                    background: linear-gradient(135deg, #6a412d, #b56a3b);
                    color: white;
                    border: 0;
                    padding: 0.8rem 1rem;
                    border-radius: 999px;
                    cursor: pointer;
                }
            </style>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

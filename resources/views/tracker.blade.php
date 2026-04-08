<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kopi Keliling Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <style>
        :root {
            --cream: #f7f0e3;
            --latte: #e8d9c5;
            --espresso: #3b2418;
            --mocha: #6a412d;
            --caramel: #b56a3b;
            --foam: rgba(255, 249, 241, 0.84);
            --leaf: #2f6b55;
            --line: rgba(76, 48, 33, 0.14);
            --shadow: 0 24px 80px rgba(59, 36, 24, 0.14);
            --danger: #9f2f20;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--espresso);
            font-family: "Manrope", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(181, 106, 59, 0.28), transparent 24%),
                radial-gradient(circle at bottom right, rgba(47, 107, 85, 0.16), transparent 22%),
                linear-gradient(145deg, #f2e6d5 0%, #f7f0e3 45%, #efe6d7 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 56px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255, 250, 242, 0.68);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 18px;
            z-index: 20;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--mocha), var(--caramel));
            font-family: "Cormorant Garamond", serif;
            font-size: 1.35rem;
        }

        .nav-links {
            position: relative;
        }

        .menu-toggle {
            width: 54px;
            height: 54px;
            border: 1px solid var(--line);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.62);
            display: inline-grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 16px 30px rgba(59, 36, 24, 0.1);
            transition: transform 180ms ease, background 180ms ease;
        }

        .menu-toggle:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.82);
        }

        .menu-toggle span {
            display: block;
            width: 20px;
            height: 2px;
            border-radius: 999px;
            background: var(--espresso);
            position: relative;
        }

        .menu-toggle span::before,
        .menu-toggle span::after {
            content: "";
            position: absolute;
            left: 0;
            width: 20px;
            height: 2px;
            border-radius: 999px;
            background: var(--espresso);
        }

        .menu-toggle span::before {
            top: -6px;
        }

        .menu-toggle span::after {
            top: 6px;
        }

        .menu-dropdown {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            width: min(320px, calc(100vw - 40px));
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 26px;
            background: rgba(255, 249, 241, 0.96);
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px);
            transition: opacity 180ms ease, transform 180ms ease;
        }

        .menu-dropdown.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .menu-dropdown-header {
            padding: 14px 14px 10px;
        }

        .menu-dropdown-header strong {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .menu-dropdown-header small {
            color: rgba(59, 36, 24, 0.66);
        }

        .menu-items {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }

        .menu-items a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(76, 48, 33, 0.08);
            transition: transform 180ms ease, background 180ms ease;
        }

        .menu-items a:hover {
            transform: translateX(2px);
            background: rgba(232, 217, 197, 0.52);
        }

        .menu-items span {
            color: rgba(59, 36, 24, 0.6);
            font-size: 0.9rem;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
            padding-top: 28px;
            align-items: stretch;
        }

        .hero-copy,
        .hero-card,
        .panel,
        .story-card,
        .menu-card {
            border: 1px solid var(--line);
            border-radius: 32px;
            background: var(--foam);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
        }

        .hero-copy {
            padding: 34px;
            position: relative;
            overflow: hidden;
        }

        .hero-copy::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -70px;
            top: -90px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181, 106, 59, 0.28), transparent 68%);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(181, 106, 59, 0.12);
            color: var(--mocha);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        h1, h2, h3 {
            margin: 0;
            font-family: "Cormorant Garamond", serif;
            line-height: 0.96;
        }

        h1 {
            margin-top: 18px;
            font-size: clamp(3.2rem, 8vw, 6.4rem);
            max-width: 9ch;
        }

        .lead {
            margin: 18px 0 0;
            max-width: 58ch;
            font-size: 1.04rem;
            line-height: 1.8;
            color: rgba(59, 36, 24, 0.78);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 20px;
            border-radius: 999px;
            font-weight: 700;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button.primary {
            color: #fff;
            background: linear-gradient(135deg, var(--mocha), var(--caramel));
            box-shadow: 0 18px 32px rgba(106, 65, 45, 0.24);
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.6);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .hero-stat {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.56);
            border: 1px solid rgba(106, 65, 45, 0.09);
        }

        .hero-stat span {
            display: block;
            margin-bottom: 8px;
            font-size: 0.78rem;
            color: rgba(59, 36, 24, 0.65);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-card {
            padding: 28px;
            display: grid;
            gap: 18px;
            align-content: start;
            background:
                linear-gradient(165deg, rgba(59, 36, 24, 0.94), rgba(106, 65, 45, 0.92)),
                var(--foam);
            color: #fff6ef;
        }

        .hero-card h2 {
            font-size: clamp(2.2rem, 5vw, 3.4rem);
        }

        .hero-card p {
            margin: 0;
            line-height: 1.8;
            color: rgba(255, 246, 239, 0.82);
        }

        .status {
            padding: 14px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #fff2e8;
        }

        .status.error {
            background: rgba(159, 47, 32, 0.18);
            color: #ffe6df;
        }

        .status.success {
            background: rgba(47, 107, 85, 0.22);
            color: #ebfff4;
        }

        .section {
            margin-top: 24px;
            display: grid;
            grid-template-columns: 0.72fr 1.28fr;
            gap: 24px;
        }

        .story-card,
        .menu-card,
        .panel {
            padding: 26px;
        }

        .story-card h3,
        .menu-card h3,
        .panel h3 {
            font-size: clamp(2rem, 4vw, 2.8rem);
            margin-bottom: 14px;
        }

        .story-card p,
        .menu-card p,
        .panel p {
            color: rgba(59, 36, 24, 0.78);
            line-height: 1.8;
        }

        .bean-list,
        .menu-list,
        .feature-list,
        .history-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .bean-list,
        .feature-list {
            display: grid;
            gap: 12px;
        }

        .bean-list li,
        .feature-list li {
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.6);
        }

        .menu-card {
            display: grid;
            gap: 18px;
        }

        .menu-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .menu-list li {
            padding: 18px;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.74), rgba(232, 217, 197, 0.72));
            border: 1px solid var(--line);
        }

        .menu-list strong {
            display: block;
            margin-bottom: 8px;
        }

        .tracking {
            margin-top: 24px;
            display: grid;
            grid-template-columns: 1.18fr 0.82fr;
            gap: 24px;
        }

        .map-panel {
            overflow: hidden;
            padding: 0;
        }

        .map-header {
            padding: 24px 24px 18px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: start;
        }

        .map-header p {
            margin: 10px 0 0;
        }

        .live-pill {
            padding: 10px 14px;
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--leaf), #4b9a7b);
            box-shadow: 0 14px 30px rgba(47, 107, 85, 0.22);
            white-space: nowrap;
        }

        #map {
            height: 560px;
            width: 100%;
        }

        .side-stack {
            display: grid;
            gap: 24px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .meta-item {
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.64);
        }

        .meta-item span {
            display: block;
            margin-bottom: 8px;
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(59, 36, 24, 0.62);
        }

        .history-list {
            margin-top: 18px;
            display: grid;
            gap: 12px;
            max-height: 320px;
            overflow: auto;
        }

        .history-list li {
            padding: 16px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.64);
        }

        .history-list small {
            color: rgba(59, 36, 24, 0.62);
        }

        footer {
            margin-top: 28px;
            padding: 22px 8px 0;
            text-align: center;
            color: rgba(59, 36, 24, 0.62);
        }

        @media (max-width: 980px) {
            .hero,
            .section,
            .tracking {
                grid-template-columns: 1fr;
            }

            .menu-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page {
                width: min(100% - 20px, 1200px);
                padding-top: 14px;
            }

            .nav {
                border-radius: 28px;
                padding: 16px;
                align-items: center;
                flex-direction: row;
            }

            .menu-dropdown {
                right: -4px;
            }

            .hero-copy,
            .hero-card,
            .story-card,
            .menu-card,
            .panel {
                border-radius: 26px;
                padding: 22px;
            }

            .hero-stats,
            .meta-grid {
                grid-template-columns: 1fr;
            }

            #map {
                height: 380px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <nav class="nav">
            <a href="#beranda" class="brand">
                <span class="brand-badge">K</span>
                <span>Kopi Keliling</span>
            </a>
            <div class="nav-links">
                <button class="menu-toggle" id="menu-toggle" type="button" aria-expanded="false" aria-controls="nav-menu" aria-label="Buka menu navigasi">
                    <span></span>
                </button>
                <div class="menu-dropdown" id="nav-menu">
                    <div class="menu-dropdown-header">
                        <strong>Menu Kopi Keliling</strong>
                        <small>Jelajahi katalog, akun, dan lokasi gerobak.</small>
                    </div>
                    <ul class="menu-items">
                        <li>
                            <a href="#menu">
                                <strong>Katalog Menu Kopi</strong>
                                <span>Lihat menu</span>
                            </a>
                        </li>
                        @guest
                            <li>
                                <a href="{{ Route::has('login') ? route('login') : '#' }}">
                                    <strong>Login</strong>
                                    <span>Masuk akun</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ Route::has('register') ? route('register') : '#' }}">
                                    <strong>Register</strong>
                                    <span>Buat akun</span>
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('dashboard') }}">
                                    <strong>Profil</strong>
                                    <span>Akun dan pembelian</span>
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        style="width: 100%; text-align: left; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; border-radius: 18px; background: rgba(255, 255, 255, 0.72); border: 1px solid rgba(76, 48, 33, 0.08); font: inherit; color: inherit; cursor: pointer;"
                                    >
                                        <strong>Logout</strong>
                                        <span>Keluar akun</span>
                                    </button>
                                </form>
                            </li>
                        @endguest
                        <li>
                            <a href="#cerita">
                                <strong>Tentang</strong>
                                <span>Cerita brand</span>
                            </a>
                        </li>
                        <li>
                            <a href="#lacak">
                                <strong>Lokasi Gerobak Terdekat</strong>
                                <span>Lacak sekarang</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="hero" id="beranda">
            <div class="hero-copy">
                <span class="eyebrow">Coffee Cart Live Tracker</span>
                <h1>Kopi hangat yang bisa kamu kejar di peta.</h1>
                <p class="lead">
                    Landing page ini dibuat seperti website coffee shop, tetapi intinya tetap
                    praktis: pelanggan bisa melihat gerobak kopi sedang berada di mana melalui
                    Leaflet map yang terus diperbarui dari data Traccar Android yang dikirim ke Laravel.
                </p>
                <div class="hero-actions">
                    <a href="#lacak" class="button primary">Lacak Gerobak Sekarang</a>
                    <a href="#menu" class="button secondary">Lihat Menu Andalan</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span>Titik Tersimpan</span>
                        <strong id="points-count">{{ count($locations) }}</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Status</span>
                        <strong>Gerobak Aktif</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Rasa</span>
                        <strong>Espresso to Street</strong>
                    </div>
                </div>
            </div>

            <aside class="hero-card">
                <span class="eyebrow" style="background: rgba(255,255,255,0.12); color: #fff;">Realtime Update</span>
                <h2>Temukan aroma kopi terdekat.</h2>
                <p>
                    Aplikasi Traccar Android mengirim posisi gerobak ke endpoint Laravel ini.
                    Peta publik akan membaca data tersebut secara berkala, sehingga marker selalu
                    menampilkan titik terbaru yang dikirim dari HP penjual.
                </p>
                <div id="status" class="status">Menunggu data lokasi terbaru dari Traccar.</div>
                <ul class="feature-list">
                    <li>Leaflet map untuk melihat posisi gerobak secara visual.</li>
                    <li>Kompatibel dengan format JSON Traccar Client 9+ dan query/form OsmAnd.</li>
                    <li>Riwayat titik lokasi untuk melihat jejak rute gerobak.</li>
                </ul>
            </aside>
        </section>

        <section class="section" id="cerita">
            <article class="story-card">
                <h3>Dibuat untuk kopi jalanan yang dinamis.</h3>
                <p>
                    Konsepnya seperti coffee shop modern, tetapi lebih luwes karena barista
                    bergerak langsung ke area pelanggan. Website ini jadi etalase digital
                    sekaligus alat lacak gerobak.
                </p>
                <ul class="bean-list">
                    <li><strong>Single Origin Feel</strong><br>Warna hangat, tipografi klasik, dan nuansa artisan coffee.</li>
                    <li><strong>Street Ready</strong><br>Pelanggan cukup buka website untuk melihat lokasi gerobak saat itu juga.</li>
                    <li><strong>Built on Laravel</strong><br>Data lokasi disimpan rapi agar mudah dikembangkan lagi nanti.</li>
                </ul>
            </article>

            <article class="menu-card" id="menu">
                <h3>Menu yang cocok dijual dari gerobak.</h3>
                <p>
                    Landing page coffee shop terasa lebih hidup kalau ada sentuhan menu.
                    Bagian ini bikin pengunjung langsung paham vibe brand-mu sebelum melihat peta.
                </p>
                <ul class="menu-list">
                    <li>
                        <strong>Espresso Cart</strong>
                        Shot pekat untuk pelanggan yang buru-buru tapi butuh tenaga.
                    </li>
                    <li>
                        <strong>Brown Sugar Latte</strong>
                        Manis, creamy, dan paling gampang jadi favorit pelanggan jalanan.
                    </li>
                    <li>
                        <strong>Cold Brew Mobile</strong>
                        Segar untuk area kampus, taman kota, dan event luar ruangan.
                    </li>
                </ul>
            </article>
        </section>

        <section class="tracking" id="lacak">
            <div class="panel map-panel">
                <div class="map-header">
                    <div>
                        <span class="eyebrow">Leaflet Tracking Map</span>
                        <h3 style="margin-top: 12px;">Lacak keberadaan gerobak kopi.</h3>
                        <p>
                            Marker akan bergerak otomatis mengikuti lokasi paling baru dari HP penjual.
                            Garis rute membantu pelanggan melihat arah pergerakan gerobak.
                        </p>
                    </div>
                    <div class="live-pill">Live Gerobak</div>
                </div>
                <div id="map"></div>
            </div>

            <div class="side-stack">
                <aside class="panel">
                    <span class="eyebrow">Lokasi Terbaru</span>
                    <h3 style="margin-top: 12px;">Sekarang gerobak ada di sini.</h3>
                    <div class="meta-grid" style="margin-top: 18px;">
                        <div class="meta-item">
                            <span>Device ID</span>
                            <strong id="current-device">{{ $latestLocation['device_id'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Latitude</span>
                            <strong id="current-lat">{{ $latestLocation['latitude'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Longitude</span>
                            <strong id="current-lng">{{ $latestLocation['longitude'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Update Terakhir</span>
                            <strong id="recorded-at">{{ $latestLocation['recorded_at'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Baterai</span>
                            <strong id="current-battery">
                                {{ isset($latestLocation['battery_level']) ? $latestLocation['battery_level'].'%' : '-' }}
                            </strong>
                        </div>
                        <div class="meta-item">
                            <span>Akurasi</span>
                            <strong id="current-accuracy">
                                {{ isset($latestLocation['accuracy']) ? $latestLocation['accuracy'].' m' : '-' }}
                            </strong>
                        </div>
                        <div class="meta-item">
                            <span>Status Gerak</span>
                            <strong id="current-moving">
                                @if (array_key_exists('is_moving', $latestLocation ?? []))
                                    {{ $latestLocation['is_moving'] ? 'Sedang bergerak' : 'Sedang diam' }}
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                    </div>
                </aside>

                <aside class="panel">
                    <span class="eyebrow">Riwayat Gerak</span>
                    <h3 style="margin-top: 12px;">Jejak titik lokasi gerobak.</h3>
                    <p>Daftar ini hanya menampilkan data lokasi yang berhasil dikirim dari Traccar.</p>
                    <ul id="history-list" class="history-list"></ul>
                </aside>

                <aside class="panel">
                    <span class="eyebrow">URL Traccar</span>
                    <h3 style="margin-top: 12px;">Alamat server untuk aplikasi Traccar.</h3>
                    <p>Masukkan URL penuh ini ke field <strong>Server URL</strong> di aplikasi Traccar Android.</p>
                    <div style="padding: 14px 16px; border-radius: 18px; background: rgba(255,255,255,0.72); border: 1px solid rgba(76, 48, 33, 0.1); word-break: break-all; font-weight: 700; color: #3b2418;">
                        {{ $traccarEndpoint }}
                    </div>
                    <p style="margin-top: 14px;">
                        Isi <strong>Device Identifier</strong> di Traccar dengan ID unik, misalnya
                        <code>gerobak-kopi-01</code>.
                    </p>
                </aside>
            </div>
        </section>

        <footer>
            Kopi Keliling Tracker menggunakan Laravel, Traccar Android, dan Leaflet.js untuk membantu pelanggan menemukan gerobak kopi.
        </footer>
    </div>

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>
    <script>
        const state = {
            locations: @json($locations),
            marker: null,
            polyline: null,
            map: null,
        };

        const endpoints = {
            latest: @json(route('api.location.latest')),
        };

        const map = L.map('map', {
            zoomControl: false,
        }).setView([-2.5489, 118.0149], 5);
        state.map = map;

        L.control.zoom({
            position: 'bottomright',
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const coffeeIcon = L.divIcon({
            className: 'coffee-marker',
            html: `
                <div style="
                    width: 26px;
                    height: 26px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #6a412d, #b56a3b);
                    border: 3px solid #fff7ed;
                    box-shadow: 0 10px 18px rgba(59, 36, 24, 0.28);
                "></div>
            `,
            iconSize: [26, 26],
            iconAnchor: [13, 13],
        });

        const menuToggle = document.getElementById('menu-toggle');
        const navMenu = document.getElementById('nav-menu');

        function closeMenu() {
            navMenu.classList.remove('open');
            menuToggle.setAttribute('aria-expanded', 'false');
        }

        function toggleMenu() {
            const isOpen = navMenu.classList.toggle('open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
        }

        menuToggle.addEventListener('click', toggleMenu);

        document.addEventListener('click', (event) => {
            if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });

        navMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });

        function setStatus(message, type = 'info') {
            const el = document.getElementById('status');
            el.className = `status ${type}`;
            el.textContent = message;
        }

        function updateSummary(location) {
            document.getElementById('current-device').textContent = location?.device_id || '-';
            document.getElementById('current-lat').textContent = location ? location.latitude : '-';
            document.getElementById('current-lng').textContent = location ? location.longitude : '-';
            document.getElementById('recorded-at').textContent = location ? (location.recorded_at || 'Belum tersimpan') : '-';
            document.getElementById('current-battery').textContent = location && location.battery_level !== null
                ? `${location.battery_level}%`
                : '-';
            document.getElementById('current-accuracy').textContent = location && location.accuracy !== null
                ? `${location.accuracy} m`
                : '-';
            document.getElementById('current-moving').textContent = location && location.is_moving !== null
                ? (location.is_moving ? 'Sedang bergerak' : 'Sedang diam')
                : '-';
            document.getElementById('points-count').textContent = state.locations.length;
        }

        function renderHistory() {
            const list = document.getElementById('history-list');
            list.innerHTML = '';

            if (!state.locations.length) {
                const item = document.createElement('li');
                item.textContent = 'Belum ada data lokasi tersimpan.';
                list.appendChild(item);
                return;
            }

            [...state.locations].reverse().forEach((location) => {
                const item = document.createElement('li');
                item.innerHTML = `
                    <strong>${location.device_id || 'Traccar Device'}</strong><br>
                    ${location.latitude}, ${location.longitude}<br>
                    <small>${location.recorded_at ?? 'Belum tersimpan ke database'}</small>
                `;
                list.appendChild(item);
            });
        }

        function renderMap() {
            const latLngs = state.locations.map((location) => [location.latitude, location.longitude]);

            if (state.marker) {
                state.marker.remove();
                state.marker = null;
            }

            if (state.polyline) {
                state.polyline.remove();
                state.polyline = null;
            }

            if (!latLngs.length) {
                renderHistory();
                updateSummary(null);
                return;
            }

            const latest = state.locations[state.locations.length - 1] || null;

            if (latest) {
                state.marker = L.marker([latest.latitude, latest.longitude], {
                    icon: coffeeIcon,
                }).addTo(map).bindPopup(`
                    <strong>Gerobak Kopi</strong><br>
                    Device: ${latest.device_id || '-'}<br>
                    Lat: ${latest.latitude}<br>
                    Lng: ${latest.longitude}<br>
                    Battery: ${latest.battery_level !== null ? `${latest.battery_level}%` : '-'}<br>
                    Updated: ${latest.recorded_at || '-'}
                `);
            }

            if (latLngs.length) {
                state.polyline = L.polyline(latLngs, {
                    color: '#6a412d',
                    weight: 4,
                    opacity: 0.88,
                }).addTo(map);
            }

            if (latLngs.length === 1) {
                map.setView(latLngs[0], 17);
            } else if (latLngs.length > 1) {
                map.fitBounds(latLngs, { padding: [24, 24] });
            }

            renderHistory();
            updateSummary(latest);
        }

        async function refreshLocations() {
            const response = await fetch(endpoints.latest, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Gagal mengambil lokasi terbaru.');
            }

            state.locations = payload.locations || [];
            renderMap();

            if (payload.latest) {
                setStatus('Peta berhasil diperbarui dari data Traccar terbaru.', 'success');
            } else {
                setStatus('Belum ada data masuk dari Traccar. Pastikan aplikasi sudah mengirim lokasi.', 'info');
            }
        }

        renderMap();
        refreshLocations().catch((error) => setStatus(error.message, 'error'));
        setInterval(() => {
            refreshLocations().catch((error) => setStatus(error.message, 'error'));
        }, 8000);
    </script>
</body>
</html>

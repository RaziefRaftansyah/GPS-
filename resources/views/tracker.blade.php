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

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 18%, rgba(255, 255, 255, 0.48), transparent 18%),
                radial-gradient(circle at 82% 10%, rgba(181, 106, 59, 0.14), transparent 16%),
                radial-gradient(circle at 76% 78%, rgba(47, 107, 85, 0.08), transparent 18%);
            pointer-events: none;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            position: relative;
            width: calc(100% - 10px);
            max-width: none;
            margin: 0 auto;
            padding: 6px 0 40px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 24px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255, 250, 242, 0.78);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 6px;
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
            grid-template-columns: minmax(0, 1.58fr) minmax(400px, 0.82fr);
            gap: 16px;
            padding-top: 10px;
            align-items: stretch;
            min-height: calc(100vh - 82px);
        }

        .hero-copy,
        .hero-card,
        .panel,
        .story-card,
        .menu-card {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: rgba(255, 249, 241, 0.9);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
        }

        .hero-copy {
            padding: 52px 46px 30px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hero-copy::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            right: -96px;
            top: -120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181, 106, 59, 0.28), transparent 68%);
        }

        .hero-copy::before {
            content: "";
            position: absolute;
            left: -120px;
            bottom: -150px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.36), transparent 66%);
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
            margin-top: 14px;
            font-size: clamp(4.8rem, 8vw, 9rem);
            max-width: 7ch;
        }

        .lead {
            margin: 20px 0 0;
            max-width: 72ch;
            font-size: 1.12rem;
            line-height: 1.95;
            color: rgba(59, 36, 24, 0.78);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 32px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 22px;
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
            margin-top: 38px;
        }

        .hero-stat {
            padding: 20px 18px;
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
            min-height: 100%;
            padding: 34px 30px;
            display: grid;
            gap: 16px;
            align-content: space-between;
            background:
                linear-gradient(165deg, rgba(59, 36, 24, 0.94), rgba(106, 65, 45, 0.92)),
                var(--foam);
            color: #fff6ef;
        }

        .hero-card h2 {
            font-size: clamp(2.6rem, 4vw, 4.1rem);
        }

        .hero-card p {
            margin: 0;
            line-height: 1.85;
            color: rgba(255, 246, 239, 0.82);
        }

        .status {
            padding: 16px 18px;
            border-radius: 22px;
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
            margin-top: 18px;
            display: grid;
            grid-template-columns: 0.72fr 1.28fr;
            gap: 18px;
        }

        .story-card,
        .menu-card,
        .panel {
            padding: 28px;
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
            padding: 16px 18px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.6);
        }

        .feature-list li {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.12);
            color: rgba(255, 246, 239, 0.84);
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
            margin-top: 18px;
            display: grid;
            gap: 18px;
        }

        .map-panel {
            overflow: hidden;
            padding: 28px;
            display: grid;
            gap: 22px;
            background:
                radial-gradient(circle at top right, rgba(181, 106, 59, 0.14), transparent 24%),
                linear-gradient(180deg, rgba(255, 250, 242, 0.96), rgba(250, 244, 236, 0.94));
        }

        .map-header {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
        }

        .map-header-copy {
            max-width: 64ch;
        }

        .map-header h3 {
            margin-top: 12px;
            font-size: clamp(2.4rem, 4vw, 3.7rem);
        }

        .map-header p {
            margin: 12px 0 0;
        }

        .live-pill {
            padding: 11px 16px;
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--leaf), #4b9a7b);
            box-shadow: 0 14px 30px rgba(47, 107, 85, 0.22);
            white-space: nowrap;
        }

        .map-stage {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid rgba(76, 48, 33, 0.1);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.26), rgba(255, 255, 255, 0)),
                rgba(255, 252, 247, 0.8);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .map-stage::after {
            content: "";
            position: absolute;
            inset: auto 22px 18px auto;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181, 106, 59, 0.14), transparent 68%);
            pointer-events: none;
            z-index: 401;
        }

        #map {
            height: 460px;
            width: 100%;
        }

        .tracking-details {
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            gap: 18px;
            align-items: start;
        }

        .location-panel {
            display: grid;
            gap: 20px;
        }

        .location-panel h3,
        .history-panel h3,
        .endpoint-panel h3 {
            margin-top: 12px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
        }

        .meta-item {
            padding: 18px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.78), rgba(247, 240, 227, 0.72));
            box-shadow: 0 14px 24px rgba(59, 36, 24, 0.06);
        }

        .meta-item span {
            display: block;
            margin-bottom: 10px;
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(59, 36, 24, 0.62);
        }

        .meta-item strong {
            display: block;
            font-size: 1.12rem;
            line-height: 1.45;
        }

        .tracking-secondary {
            display: grid;
            gap: 24px;
        }

        .history-list {
            margin-top: 18px;
            display: grid;
            gap: 12px;
            max-height: 362px;
            overflow: auto;
            padding-right: 4px;
        }

        .history-list li {
            padding: 16px 18px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.76), rgba(247, 240, 227, 0.72));
        }

        .history-list strong {
            display: block;
            margin-bottom: 8px;
        }

        .history-coords {
            color: rgba(59, 36, 24, 0.78);
            line-height: 1.7;
        }

        .history-list small {
            display: block;
            margin-top: 8px;
            color: rgba(59, 36, 24, 0.62);
        }

        .endpoint-box {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.78), rgba(232, 217, 197, 0.6));
            border: 1px solid rgba(76, 48, 33, 0.1);
            word-break: break-all;
            font-weight: 700;
            color: #3b2418;
        }

        .endpoint-note {
            margin-top: 14px;
        }

        .leaflet-container {
            background: #f5efe6;
            font-family: "Manrope", sans-serif;
        }

        .leaflet-control-zoom {
            border: 0 !important;
            border-radius: 18px !important;
            overflow: hidden;
            box-shadow: 0 16px 28px rgba(59, 36, 24, 0.18) !important;
        }

        .leaflet-control-zoom a {
            width: 42px !important;
            height: 42px !important;
            line-height: 42px !important;
            color: var(--espresso) !important;
            background: rgba(255, 251, 246, 0.96) !important;
            border-bottom: 1px solid rgba(76, 48, 33, 0.08) !important;
        }

        .leaflet-control-attribution {
            border-radius: 14px 0 0 0;
            background: rgba(255, 249, 241, 0.88) !important;
            backdrop-filter: blur(10px);
        }

        footer {
            margin-top: 20px;
            padding: 22px 8px 0;
            text-align: center;
            color: rgba(59, 36, 24, 0.62);
        }

        @media (max-width: 980px) {
            .hero,
            .section,
            .tracking-details {
                grid-template-columns: 1fr;
            }

            .hero {
                min-height: auto;
                gap: 14px;
            }

            .menu-list {
                grid-template-columns: 1fr;
            }

            .map-header {
                flex-direction: column;
            }
        }

        @media (max-width: 640px) {
            .page {
                width: calc(100% - 10px);
                padding-top: 6px;
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

            h1 {
                font-size: clamp(3.2rem, 13vw, 4.8rem);
            }

            .hero-stats,
            .meta-grid {
                grid-template-columns: 1fr;
            }

            #map {
                height: 320px;
            }

            .map-panel {
                padding: 22px;
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
                <h3>Kopi Tracker</h3>
                <p>
                    Website ini dibuat untuk membantu pelanggan mengetahui posisi gerobak kopi
                    secara lebih mudah dan cepat. Dengan adanya pelacakan lokasi, pembeli tidak
                    perlu menebak-nebak gerobak sedang ada di mana.
                </p>
                <ul class="bean-list">
                    <li><strong>Mudah ditemukan</strong><br>Pelanggan bisa langsung melihat lokasi gerobak tanpa harus bertanya lewat chat atau telepon.</li>
                    <li><strong>Lebih efisien</strong><br>Penjual lebih mudah memberi informasi posisi terbaru kepada pelanggan secara realtime.</li>
                    <li><strong>Siap dikembangkan</strong><br>Website ini bisa jadi dasar untuk fitur lanjutan seperti status jualan, menu, dan pemesanan.</li>
                </ul>
            </article>

            <article class="menu-card" id="menu">
                <h3>Alasan website ini dibuat.</h3>
                <p>
                    Kopi Tracker bukan hanya tampilan peta, tetapi juga jadi media informasi
                    sederhana agar pelanggan dan penjual sama-sama terbantu saat gerobak
                    berpindah tempat.
                </p>
                <ul class="menu-list">
                    <li>
                        <strong>Untuk pelanggan</strong>
                        Pelanggan bisa membuka website lalu langsung melihat posisi gerobak kopi yang terbaru.
                    </li>
                    <li>
                        <strong>Untuk penjual</strong>
                        Penjual tidak perlu terus-menerus memberi update lokasi secara manual ke banyak orang.
                    </li>
                    <li>
                        <strong>Untuk pengembangan</strong>
                        Sistem ini bisa dikembangkan lagi menjadi dashboard usaha kopi keliling yang lebih lengkap.
                    </li>
                </ul>
            </article>
        </section>

        <section class="tracking" id="lacak">
            <div class="panel map-panel">
                <div class="map-header">
                    <div class="map-header-copy">
                        <span class="eyebrow">Leaflet Tracking Map</span>
                        <h3>Lacak keberadaan gerobak kopi.</h3>
                        <p>
                            Marker akan bergerak otomatis mengikuti lokasi paling baru dari HP penjual.
                            Garis rute membantu pelanggan melihat arah pergerakan gerobak.
                        </p>
                    </div>
                    <div class="live-pill">Live Gerobak</div>
                </div>
                <div class="map-stage">
                    <div id="map"></div>
                </div>
            </div>

            <div class="tracking-details">
                <aside class="panel location-panel">
                    <span class="eyebrow">Lokasi Terbaru</span>
                    <h3>Sekarang gerobak ada di sini.</h3>
                    <div class="meta-grid">
                        <div class="meta-item">
                            <span>Nama Gerobak</span>
                            <strong id="current-unit">{{ $latestLocation['unit_name'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Driver Aktif</span>
                            <strong id="current-driver">{{ $latestLocation['driver_name'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Device ID</span>
                            <strong id="current-device">{{ $latestLocation['device_id'] ?? '-' }}</strong>
                        </div>
                        <div class="meta-item">
                            <span>Kode Unit</span>
                            <strong id="current-code">{{ $latestLocation['unit_code'] ?? '-' }}</strong>
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

                <div class="tracking-secondary">
                    <aside class="panel history-panel">
                        <span class="eyebrow">Riwayat Gerak</span>
                        <h3>Jejak titik lokasi gerobak.</h3>
                        <p>Daftar ini hanya menampilkan data lokasi yang berhasil dikirim dari Traccar.</p>
                        <ul id="history-list" class="history-list"></ul>
                    </aside>

                    <aside class="panel endpoint-panel">
                        <span class="eyebrow">URL Traccar</span>
                        <h3>Alamat server untuk aplikasi Traccar.</h3>
                        <p>Masukkan URL penuh ini ke field <strong>Server URL</strong> di aplikasi Traccar Android.</p>
                        <div class="endpoint-box">
                            {{ $traccarEndpoint }}
                        </div>
                        <p class="endpoint-note">
                            Isi <strong>Device Identifier</strong> di Traccar dengan ID unik, misalnya
                            <code>gerobak-kopi-01</code>.
                        </p>
                    </aside>
                </div>
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

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20,
            detectRetina: true,
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
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
            document.getElementById('current-unit').textContent = location?.unit_name || '-';
            document.getElementById('current-driver').textContent = location?.driver_name || '-';
            document.getElementById('current-device').textContent = location?.device_id || '-';
            document.getElementById('current-code').textContent = location?.unit_code || '-';
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
                    <strong>${location.unit_name || location.device_id || 'Traccar Device'}</strong>
                    <div class="history-coords">${location.driver_name ? `Driver: ${location.driver_name}` : 'Driver belum di-assign'}</div>
                    <div class="history-coords">${location.latitude}, ${location.longitude}</div>
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
                    <strong>${latest.unit_name || 'Gerobak Kopi'}</strong><br>
                    Driver: ${latest.driver_name || '-'}<br>
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

            requestAnimationFrame(() => {
                map.invalidateSize();
            });

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

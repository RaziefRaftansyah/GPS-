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
            padding: 6px 0 24px;
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
            grid-template-columns: 1fr;
            gap: 14px;
            padding-top: 8px;
            align-items: stretch;
            min-height: auto;
        }

        .hero-copy,
        .panel,
        .story-card,
        .menu-card {
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(255, 252, 247, 0.94);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
        }

        .hero-copy {
            padding: 28px 30px 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(59, 130, 246, 0.1);
            color: #1d4ed8;
            font-size: 0.74rem;
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
            margin-top: 12px;
            font-size: clamp(2.8rem, 5vw, 4.4rem);
            max-width: 11ch;
            line-height: 1.02;
        }

        .lead {
            margin: 14px 0 0;
            max-width: 70ch;
            font-size: 0.98rem;
            line-height: 1.7;
            color: rgba(59, 36, 24, 0.72);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
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
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 18px 32px rgba(37, 99, 235, 0.2);
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.88);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 24px;
        }

        .hero-stat {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(76, 48, 33, 0.08);
        }

        .hero-stat span {
            display: block;
            margin-bottom: 8px;
            font-size: 0.78rem;
            color: rgba(59, 36, 24, 0.65);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .panel {
            padding: 22px;
        }

        .panel h3 {
            font-size: clamp(2rem, 4vw, 2.8rem);
            margin-bottom: 14px;
        }

        .panel p {
            color: rgba(59, 36, 24, 0.78);
            line-height: 1.8;
        }

        .menu-list,
        .history-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .tracking {
            margin-top: 14px;
            display: grid;
            gap: 18px;
        }

        .map-panel {
            overflow: hidden;
            padding: 22px;
            display: grid;
            gap: 18px;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
        }

        .map-shell {
            display: grid;
            grid-template-columns: minmax(260px, 0.34fr) minmax(0, 1fr);
            gap: 18px;
            align-items: stretch;
        }

        .map-overview {
            display: grid;
            align-content: start;
            gap: 14px;
        }

        .map-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .map-header-copy,
        .map-overview .map-header-copy {
            max-width: 100%;
        }

        .map-header h3 {
            margin-top: 10px;
            font-size: clamp(2rem, 3vw, 2.8rem);
        }

        .map-header p {
            margin: 10px 0 0;
            line-height: 1.7;
        }

        .live-pill {
            padding: 11px 16px;
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.22);
            white-space: nowrap;
        }

        .map-stage {
            position: relative;
            border-radius: 22px;
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
            height: 420px;
            width: 100%;
        }

        .tracking-details {
            display: block;
        }

        .history-panel h3,
        .endpoint-panel h3 {
            margin-top: 12px;
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
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.95);
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
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.95);
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
            background: rgba(255, 255, 255, 0.98) !important;
            border-bottom: 1px solid rgba(76, 48, 33, 0.08) !important;
        }

        .leaflet-control-attribution {
            border-radius: 14px 0 0 0;
            background: rgba(255, 249, 241, 0.88) !important;
            backdrop-filter: blur(10px);
        }

        footer {
            margin-top: 14px;
            padding: 12px 8px 0;
            text-align: center;
            color: rgba(59, 36, 24, 0.62);
        }

        @media (max-width: 980px) {
            .hero,
            .tracking-details,
            .map-shell {
                grid-template-columns: 1fr;
            }

            .hero {
                min-height: auto;
                gap: 14px;
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
                padding: 18px;
            }

            h1 {
                font-size: clamp(3.2rem, 13vw, 4.8rem);
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            #map {
                height: 320px;
            }

            .map-panel {
                padding: 18px;
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
                        <small>Akses login dan pelacakan gerobak aktif.</small>
                    </div>
                    <ul class="menu-items">
                        @guest
                            <li>
                                <a href="{{ Route::has('login') ? route('login') : '#' }}">
                                    <strong>Login</strong>
                                    <span>Masuk akun</span>
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
                <span class="eyebrow">Live Coffee Cart Tracker</span>
                <h1>Kopi hangat yang bisa kamu kejar di peta.</h1>
                <p class="lead">
                    Halaman ini fokus untuk menunjukkan posisi gerobak aktif secara cepat.
                    Data lokasi dikirim dari aplikasi Traccar di HP driver lalu langsung
                    ditampilkan ke peta publik.
                </p>
                <div class="hero-actions">
                    <a href="#lacak" class="button primary">Lacak Gerobak Sekarang</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span>Status</span>
                        <strong><span id="active-unit-count-hero">{{ count($activeUnits) }}</span> Gerobak Aktif</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Update</span>
                        <strong>Realtime GPS</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="tracking" id="lacak">
            <div class="panel map-panel">
                <div class="map-shell">
                    <div class="map-overview">
                        <div class="map-header">
                            <div class="map-header-copy">
                                <span class="eyebrow">Leaflet Tracking Map</span>
                                <h3>Lacak keberadaan gerobak kopi.</h3>
                                <p>
                                    Setiap marker mewakili posisi terbaru dari satu gerobak aktif.
                                    Kamu bisa melihat berapa gerobak yang sedang online langsung dari peta ini.
                                </p>
                            </div>
                            <div class="live-pill"><span id="active-unit-count">{{ count($activeUnits) }}</span> Gerobak Aktif</div>
                        </div>
                    </div>
                    <div class="map-stage">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <div class="tracking-details">
                <div class="tracking-secondary">
                    <aside class="panel history-panel">
                        <span class="eyebrow">Lokasi Tetap</span>
                        <h3>Lokasi jualan terbaru tiap gerobak.</h3>
                        <p>Panel ini hanya menampilkan posisi realtime paling baru dari setiap gerobak aktif.</p>
                        <ul id="history-list" class="history-list"></ul>
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
            activeUnits: @json($activeUnits),
            markers: [],
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

        function updateSummary() {
            document.getElementById('active-unit-count').textContent = state.activeUnits.length;
            document.getElementById('active-unit-count-hero').textContent = state.activeUnits.length;
        }

        function renderHistory() {
            const list = document.getElementById('history-list');
            list.innerHTML = '';

            if (!state.activeUnits.length) {
                const item = document.createElement('li');
                item.textContent = 'Belum ada lokasi realtime yang masuk.';
                list.appendChild(item);
                return;
            }

            [...state.activeUnits]
                .sort((a, b) => (b.recorded_at || '').localeCompare(a.recorded_at || ''))
                .forEach((location) => {
                const item = document.createElement('li');
                item.innerHTML = `
                    <strong>${location.unit_name || location.device_id || 'Traccar Device'}</strong>
                    <div class="history-coords">${location.driver_name ? `Driver: ${location.driver_name}` : 'Driver belum di-assign'}</div>
                    <div class="history-coords">${location.unit_code ? `Kode: ${location.unit_code}` : 'Kode unit belum tersedia'}</div>
                    <div class="history-coords">${location.latitude}, ${location.longitude}</div>
                    <small>${location.recorded_at ?? 'Belum tersimpan ke database'}</small>
                `;
                list.appendChild(item);
            });
        }

        function buildDisplayPositions(locations) {
            const groups = new Map();

            locations.forEach((location) => {
                const key = `${location.latitude},${location.longitude}`;

                if (!groups.has(key)) {
                    groups.set(key, []);
                }

                groups.get(key).push(location);
            });

            return locations.map((location) => {
                const key = `${location.latitude},${location.longitude}`;
                const group = groups.get(key) || [location];
                const index = group.indexOf(location);

                if (group.length === 1 || index === -1) {
                    return {
                        ...location,
                        displayLatitude: location.latitude,
                        displayLongitude: location.longitude,
                    };
                }

                const angle = (Math.PI * 2 * index) / group.length;
                const distance = 0.000045;

                return {
                    ...location,
                    displayLatitude: location.latitude + Math.sin(angle) * distance,
                    displayLongitude: location.longitude + Math.cos(angle) * distance,
                };
            });
        }

        function renderMap() {
            const displayLocations = buildDisplayPositions(state.activeUnits);
            const latLngs = displayLocations.map((location) => [location.displayLatitude, location.displayLongitude]);

            state.markers.forEach((marker) => marker.remove());
            state.markers = [];

            if (!latLngs.length) {
                renderHistory();
                updateSummary();
                return;
            }

            displayLocations.forEach((unitLocation) => {
                const marker = L.marker([unitLocation.displayLatitude, unitLocation.displayLongitude], {
                    icon: coffeeIcon,
                }).addTo(map).bindPopup(`
                    <strong>${unitLocation.unit_name || 'Gerobak Kopi'}</strong><br>
                    Driver: ${unitLocation.driver_name || '-'}<br>
                    Device: ${unitLocation.device_id || '-'}<br>
                    Lat: ${unitLocation.latitude}<br>
                    Lng: ${unitLocation.longitude}<br>
                    Battery: ${unitLocation.battery_level !== null ? `${unitLocation.battery_level}%` : '-'}<br>
                    Updated: ${unitLocation.recorded_at || '-'}
                `);

                state.markers.push(marker);
            });

            if (latLngs.length === 1) {
                map.setView(latLngs[0], 17);
            } else if (latLngs.length > 1) {
                map.fitBounds(latLngs, { padding: [24, 24] });
            }

            requestAnimationFrame(() => {
                map.invalidateSize();
            });

            renderHistory();
            updateSummary();
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
            state.activeUnits = payload.active_units || [];
            renderMap();
        }

        renderMap();
        refreshLocations().catch(() => null);
        setInterval(() => {
            refreshLocations().catch(() => null);
        }, 8000);
    </script>
</body>
</html>

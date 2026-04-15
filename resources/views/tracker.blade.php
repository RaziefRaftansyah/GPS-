<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kopi Keliling Tracker</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700;800&display=swap" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <style>
        :root {
            color-scheme: light;
            --cream: #f7f0e3;
            --latte: #e8d9c5;
            --espresso: #3b2418;
            --mocha: #6a412d;
            --caramel: #b56a3b;
            --foam: rgba(255, 249, 241, 0.84);
            --leaf: #2f6b55;
            --line: rgba(76, 48, 33, 0.14);
            --app-bg:
                radial-gradient(circle at top left, rgba(181, 106, 59, 0.24), transparent 24%),
                radial-gradient(circle at bottom right, rgba(47, 107, 85, 0.12), transparent 22%),
                linear-gradient(145deg, #f2e6d5 0%, #f7f0e3 45%, #efe6d7 100%);
            --sidebar-bg: linear-gradient(180deg, #6a412d 0%, #8a5536 52%, #b56a3b 100%);
            --panel: rgba(255, 252, 247, 0.94);
            --panel-alt: rgba(255, 249, 241, 0.82);
            --panel-border: var(--line);
            --text-main: var(--espresso);
            --text-soft: rgba(59, 36, 24, 0.66);
            --accent: #2d63e2;
            --accent-soft: rgba(45, 99, 226, 0.12);
            --shadow-lg: 0 24px 80px rgba(59, 36, 24, 0.14);
            --shadow-sm: 0 16px 32px rgba(59, 36, 24, 0.1);
            --shell-radius: 38px;
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
            font-family: Figtree, sans-serif;
            color: var(--text-main);
            background: var(--app-bg);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 18%, rgba(255, 255, 255, 0.42), transparent 18%),
                radial-gradient(circle at 82% 10%, rgba(181, 106, 59, 0.12), transparent 16%),
                radial-gradient(circle at 76% 78%, rgba(47, 107, 85, 0.07), transparent 18%);
            pointer-events: none;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        .tracker-shell {
            width: min(1520px, calc(100vw - 48px));
            margin: 32px auto;
            border-radius: var(--shell-radius);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--panel-border);
            background: rgba(255, 250, 242, 0.78);
            backdrop-filter: blur(18px);
            position: relative;
            z-index: 1;
        }

        .tracker-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 26px 34px 22px;
            border-bottom: 1px solid var(--panel-border);
            background: rgba(255, 250, 242, 0.76);
        }

        .tracker-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .tracker-brand-mark {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 800;
            background: var(--sidebar-bg);
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }

        .tracker-brand-copy strong,
        .tracker-brand-copy span {
            display: block;
        }

        .tracker-brand-copy strong {
            font-size: 1.05rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .tracker-brand-copy span {
            margin-top: 6px;
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .tracker-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .tracker-chip,
        .tracker-link,
        .tracker-auth {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            border-radius: 999px;
            border: 1px solid var(--panel-border);
            background: var(--panel);
            box-shadow: var(--shadow-sm);
            color: var(--text-main);
        }

        .tracker-chip {
            font-weight: 700;
        }

        .tracker-chip small {
            color: var(--text-soft);
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .tracker-chip strong {
            display: block;
            font-size: 0.95rem;
        }

        .tracker-link,
        .tracker-auth {
            font-weight: 700;
        }

        .tracker-auth.is-primary {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--mocha) 0%, var(--caramel) 100%);
        }

        .tracker-content {
            padding: 24px 34px 34px;
            display: grid;
            gap: 22px;
            background: rgba(255, 252, 247, 0.92);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.8fr);
            gap: 22px;
        }

        .panel,
        .hero-card,
        .stat-card {
            border: 1px solid var(--panel-border);
            border-radius: 30px;
            background: var(--panel);
            box-shadow: var(--shadow-sm);
        }

        .hero-card {
            padding: 34px;
            position: relative;
            overflow: hidden;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: auto -40px -40px auto;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181, 106, 59, 0.16), transparent 70%);
            pointer-events: none;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        .hero-card h1 {
            margin-top: 18px;
            max-width: 12ch;
            font-size: clamp(2.8rem, 5vw, 4.6rem);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .hero-card p {
            margin-top: 18px;
            max-width: 62ch;
            color: var(--text-soft);
            line-height: 1.8;
            font-size: 1rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 26px;
        }

        .button-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 22px;
            border-radius: 999px;
            font-weight: 700;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .button-link:hover {
            transform: translateY(-1px);
        }

        .button-link.primary {
            color: #fff;
            background: linear-gradient(135deg, var(--mocha) 0%, var(--caramel) 100%);
            box-shadow: 0 18px 32px rgba(106, 65, 45, 0.18);
        }

        .button-link.secondary {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--panel-border);
        }

        .hero-side {
            display: grid;
            gap: 16px;
        }

        .stat-card {
            padding: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(255, 249, 241, 0.96)),
                var(--panel);
        }

        .stat-card small,
        .stat-card strong,
        .stat-card span {
            display: block;
        }

        .stat-card small {
            color: var(--text-soft);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .stat-card strong {
            margin-top: 14px;
            font-size: clamp(2rem, 4vw, 2.8rem);
            line-height: 1;
        }

        .stat-card span {
            margin-top: 12px;
            color: var(--text-soft);
            line-height: 1.7;
        }

        .stat-card.is-highlight {
            color: #fff;
            background: linear-gradient(180deg, #6a412d 0%, #8a5536 56%, #b56a3b 100%);
            border-color: transparent;
        }

        .stat-card.is-highlight small,
        .stat-card.is-highlight span {
            color: rgba(255, 249, 241, 0.8);
        }

        .map-layout {
            display: grid;
            grid-template-columns: minmax(300px, 0.68fr) minmax(0, 1.32fr);
            gap: 22px;
            align-items: start;
        }

        .panel {
            padding: 28px;
        }

        .panel h2,
        .panel h3 {
            margin-top: 14px;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.04;
            letter-spacing: -0.03em;
        }

        .panel p {
            margin-top: 12px;
            color: var(--text-soft);
            line-height: 1.8;
        }

        .overview-points {
            margin-top: 24px;
            display: grid;
            gap: 12px;
        }

        .overview-point {
            padding: 16px 18px;
            border-radius: 22px;
            background: var(--panel-alt);
            border: 1px solid var(--panel-border);
        }

        .overview-point strong,
        .overview-point span {
            display: block;
        }

        .overview-point strong {
            font-size: 0.98rem;
        }

        .overview-point span {
            margin-top: 8px;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        .map-panel {
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(181, 106, 59, 0.12), transparent 24%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 243, 236, 0.96));
        }

        .map-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 999px;
            background: rgba(47, 107, 85, 0.12);
            color: var(--leaf);
            font-weight: 700;
            white-space: nowrap;
        }

        .map-stage {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(76, 48, 33, 0.1);
            background: rgba(255, 252, 247, 0.88);
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
            width: 100%;
            height: 560px;
        }

        .history-list {
            list-style: none;
            margin: 24px 0 0;
            padding: 0;
            display: grid;
            gap: 12px;
            max-height: 460px;
            overflow: auto;
        }

        .history-list li {
            padding: 18px;
            border-radius: 20px;
            border: 1px solid var(--panel-border);
            background: rgba(255, 255, 255, 0.92);
        }

        .history-list strong {
            display: block;
            font-size: 1rem;
        }

        .history-coords {
            margin-top: 8px;
            color: var(--text-soft);
            line-height: 1.7;
            font-size: 0.92rem;
        }

        .history-list small {
            display: block;
            margin-top: 10px;
            color: rgba(59, 36, 24, 0.56);
        }

        footer {
            padding: 4px 6px 2px;
            text-align: center;
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .leaflet-container {
            background: #f5efe6;
            font-family: Figtree, sans-serif;
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

        @media (max-width: 1100px) {
            .hero-grid,
            .map-layout {
                grid-template-columns: 1fr;
            }

            #map {
                height: 440px;
            }
        }

        @media (max-width: 760px) {
            .tracker-shell {
                width: min(100vw - 24px, 100%);
                margin: 12px auto;
                border-radius: 28px;
            }

            .tracker-topbar,
            .tracker-content {
                padding-left: 18px;
                padding-right: 18px;
            }

            .tracker-topbar {
                padding-top: 18px;
                padding-bottom: 18px;
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-card,
            .panel,
            .stat-card {
                padding: 20px;
                border-radius: 24px;
            }

            .tracker-topbar-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .tracker-chip,
            .tracker-link,
            .tracker-auth,
            .button-link {
                width: 100%;
                justify-content: center;
            }

            .map-panel-header {
                flex-direction: column;
            }

            #map {
                height: 340px;
            }
        }
    </style>
</head>
<body>
    <div class="tracker-shell">
        <header class="tracker-topbar">
            <div class="tracker-brand">
                <div class="tracker-brand-mark">K</div>
                <div class="tracker-brand-copy">
                    <strong>Kopi Keliling</strong>
                    <span>Peta publik dengan bahasa visual yang selaras dengan dashboard internal.</span>
                </div>
            </div>

            <div class="tracker-topbar-actions">
                <div class="tracker-chip">
                    <div>
                        <small>Status</small>
                        <strong><span id="active-unit-count">{{ count($activeUnits) }}</span> gerobak aktif</strong>
                    </div>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="tracker-link">Buka Dashboard</a>

                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="tracker-auth">Logout</button>
                    </form>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="tracker-auth is-primary">Login Dashboard</a>
                    @endif
                @endauth
            </div>
        </header>

        <main class="tracker-content">
            <section class="hero-grid" id="beranda">
                <article class="hero-card">
                    <span class="eyebrow">Live Tracker</span>
                    <h1>Peta publik yang sekarang terasa satu keluarga dengan dashboard.</h1>
                    <p>
                        Pantau posisi gerobak kopi aktif dari halaman depan dengan tampilan yang memakai bahasa visual
                        yang sama seperti area dashboard: hangat, lembut, dan fokus pada data realtime yang penting.
                    </p>

                    <div class="hero-actions">
                        <a href="#lacak" class="button-link primary">Lihat Peta Sekarang</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="button-link secondary">Masuk ke Dashboard</a>
                        @elseif (Route::has('login'))
                            <a href="{{ route('login') }}" class="button-link secondary">Masuk sebagai Admin</a>
                        @endif
                    </div>
                </article>

                <div class="hero-side">
                    <article class="stat-card is-highlight">
                        <small>Gerobak Aktif</small>
                        <strong><span id="active-unit-count-hero">{{ count($activeUnits) }}</span></strong>
                        <span>Unit yang sedang mengirim lokasi terbaru ke peta publik saat ini.</span>
                    </article>

                    <article class="stat-card">
                        <small>Pembaruan Lokasi</small>
                        <strong>Realtime</strong>
                        <span>Data GPS akan diambil ulang secara berkala tanpa perlu refresh halaman.</span>
                    </article>
                </div>
            </section>

            <section class="map-layout" id="lacak">
                <aside class="panel">
                    <span class="eyebrow">Ringkasan Tracking</span>
                    <h2>Lacak posisi jualan terbaru dari tiap gerobak.</h2>
                    <p>
                        Halaman ini menampilkan lokasi paling baru dari setiap unit aktif. Cocok untuk pelanggan yang
                        ingin cepat tahu gerobak mana yang sedang online dan di mana posisinya sekarang.
                    </p>

                    <div class="overview-points">
                        <div class="overview-point">
                            <strong>Status unit aktif</strong>
                            <span>Jumlah gerobak online akan menyesuaikan otomatis dari data terbaru.</span>
                        </div>

                        <div class="overview-point">
                            <strong>Sinkron dengan dashboard</strong>
                            <span>Nuansa warna, panel, dan permukaan sekarang mengikuti tema dashboard internal.</span>
                        </div>

                        <div class="overview-point">
                            <strong>Riwayat posisi terbaru</strong>
                            <span>Daftar di bawah memudahkan melihat nama unit, driver, dan waktu update terakhir.</span>
                        </div>
                    </div>
                </aside>

                <section class="panel map-panel">
                    <div class="map-panel-header">
                        <div>
                            <span class="eyebrow">Leaflet Tracking Map</span>
                            <h3>Peta realtime gerobak kopi.</h3>
                            <p>Setiap marker mewakili posisi terbaru dari satu gerobak aktif yang sedang online.</p>
                        </div>

                        <div class="live-pill">GPS aktif dan diperbarui otomatis</div>
                    </div>

                    <div class="map-stage">
                        <div id="map"></div>
                    </div>
                </section>
            </section>

            <section class="panel">
                <span class="eyebrow">Lokasi Terbaru</span>
                <h3>Daftar posisi terakhir per gerobak aktif.</h3>
                <p>Panel ini akan selalu menampilkan pembaruan lokasi terbaru yang berhasil masuk ke sistem.</p>
                <ul id="history-list" class="history-list"></ul>
            </section>

            <footer>
                Kopi Keliling Tracker menggunakan Laravel, Traccar Android, dan Leaflet.js untuk membantu pelanggan menemukan gerobak kopi.
            </footer>
        </main>
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
                    Accept: 'application/json',
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

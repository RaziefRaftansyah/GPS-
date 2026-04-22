<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $assetBase = rtrim(request()->getBasePath(), '/');
        $assetPath = static fn (string $path): string => ($assetBase !== '' ? $assetBase : '').'/'.ltrim($path, '/');

        $heroCustomPath = public_path('images/hero-user.jpg');
        $heroBannerImage = file_exists($heroCustomPath)
            ? $assetPath('images/hero-user.jpg').'?v='.filemtime($heroCustomPath)
            : $assetPath('images/coffee-hero-banner-new.png');
    @endphp
    <title>Kopi Keliling Tracker</title>
    <link rel="icon" type="image/png" href="{{ $assetPath('images/ada-coffee-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700;800&display=swap" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <link rel="stylesheet" href="{{ $assetPath('css/tracker.css') }}">
</head>
<body
    data-tracker-latest-endpoint="{{ route('api.location.latest') }}"
    data-hero-banner-image="{{ $heroBannerImage }}"
>
    <div class="tracker-shell">
        <header class="tracker-topbar">
            <div class="tracker-brand">
                <img
                    class="tracker-brand-mark"
                    src="{{ $assetPath('images/ada-coffee-logo.png') }}"
                    alt="AD.A Coffee"
                >
                <div class="tracker-brand-copy">
                    <strong>AD.A Coffee</strong>
                    <span>Peta publik dengan bahasa visual yang selaras dengan dashboard internal.</span>
                </div>
            </div>

            <button
                class="mobile-nav-toggle"
                id="mobile-nav-toggle"
                type="button"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                aria-controls="tracker-nav-actions"
            >
                <span></span>
            </button>

            <div class="tracker-topbar-actions" id="tracker-nav-actions">
                <a href="#lacak" class="tracker-link">Lacak</a>
                <a href="#menu" class="tracker-link">Menu</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="tracker-auth is-primary">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="tracker-inline-form">
                        @csrf
                        <button type="submit" class="tracker-auth">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="tracker-auth is-primary">Login</a>
                @endauth
            </div>
        </header>

        <main class="tracker-content">
            <section class="hero-showcase" id="beranda">
                <div class="hero-landing-grid">
                    <section class="hero-description-panel">
                        <span class="eyebrow">Kopi Keliling Tracker</span>
                        <h1>Kopling terdekat langsung terlihat.</h1>
                        <p>
                            Temukan gerobak Kopi Keliling dari halaman depan. Panel utama di atas merangkum info
                            lokasi, lalu card peta di tengah halaman menampilkan posisimu, Kopling aktif, dan jarak
                            terdekat secara realtime dari browser.
                        </p>

                        <div class="hero-actions">
                            <a href="#lacak" class="button-link primary js-scroll-map-center">Temukan Kami</a>
                            <a href="#menu" class="button-link secondary">Katalog Kopi</a>
                        </div>
                    </section>
                </div>
            </section>

            <section class="hero-gps-panel between-map-section slide-in-up" id="lacak">
                <div class="hero-map-stats">
                    <article class="hero-map-stat-card">
                        <small>Kopling Terdekat</small>
                        <strong id="nearest-distance">Mencari...</strong>
                        <span id="nearest-copy">Aktifkan izin lokasi browser untuk melihat jarak gerobak paling dekat.</span>
                    </article>

                    <article class="hero-map-stat-card">
                        <small>Kopling Tersedia</small>
                        <strong><span id="active-unit-count-hero">{{ count($activeUnits) }}</span></strong>
                        <span>Unit Kopi Keliling yang sedang aktif dan mengirim lokasi terbaru.</span>
                    </article>
                </div>

                <div class="hero-map-frame">
                    <div id="map"></div>
                </div>
            </section>

            <section class="panel about-section slide-in-up" id="tentang">
                <div class="about-copy">
                    <span class="eyebrow">About Us</span>
                    <h2>Kopling lahir dari gerobak kopi yang dekat dengan pelanggan.</h2>
                    <p>
                        Kopi Keliling bukan hanya soal menjual minuman, tetapi tentang membuat kopi lebih mudah
                        ditemukan di titik ramai kota. Melalui tracker GPS ini, pelanggan bisa melihat gerobak
                        yang aktif, mendekat ke lokasi terdekat, dan menikmati kopi tanpa harus menebak posisi penjual.
                    </p>
                    <p>
                        Kami menggabungkan gerobak sederhana, menu yang familiar, dan teknologi realtime agar usaha
                        kecil terasa lebih modern, transparan, dan siap menjangkau lebih banyak pelanggan.
                    </p>

                </div>

                <div class="about-photo-wrap">
                    <img
                        class="about-photo"
                        src="{{ $assetPath('images/about-cart.jpg') }}"
                        alt="Gerobak kopi keliling"
                    >
                </div>
            </section>

            <section class="panel menu-section slide-in-up" id="menu">
                <div class="menu-section-header">
                    <div>
                        <span class="eyebrow">Katalog Menu</span>
                        <h2>Menu gerobak yang siap menemani hari.</h2>
                        <p>
                            Pilih menu favorit AD.A Coffee langsung dari katalog ini. Dari kopi hitam yang ringan,
                            signature gula aren, sampai non coffee yang manis dan creamy.
                        </p>
                    </div>

                    <div class="menu-note">
                        <strong>
                            @if ($menuStartingPrice !== null)
                                Mulai Rp{{ number_format((int) $menuStartingPrice, 0, ',', '.') }}
                            @else
                                Katalog sedang disiapkan
                            @endif
                        </strong>
                        <span>Harga dan varian menu mengikuti katalog terbaru dari dashboard owner.</span>
                    </div>
                </div>

                <div class="menu-showcase">
                    <div class="menu-list">
                        @forelse ($menuCatalog as $menu)
                            @php
                                $imagePath = $menu->image_path;
                                $menuImage = blank($imagePath)
                                    ? $assetPath('images/about-cart.jpg')
                                    : (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                                        ? $imagePath
                                        : $assetPath(ltrim($imagePath, '/')));
                            @endphp
                            <article class="menu-card {{ $loop->even ? 'is-reverse' : '' }}">
                                <div class="menu-card-image-wrap">
                                    <img
                                        class="menu-card-image"
                                        src="{{ $menuImage }}"
                                        alt="{{ $menu->name }}"
                                    >
                                    <span class="menu-badge">{{ $menu->category }}</span>
                                </div>
                                <div class="menu-card-body">
                                    <div class="menu-card-title-row">
                                        <h3>{{ $menu->name }}</h3>
                                        <span class="menu-price">Rp{{ number_format((int) $menu->price, 0, ',', '.') }}</span>
                                    </div>
                                    <p>{{ $menu->description ?: 'Menu favorit pelanggan Kopi Keliling siap dipesan dari gerobak terdekat.' }}</p>
                                    @if (! empty($menu->tags))
                                        <div class="menu-tags">
                                            @foreach ($menu->tags as $tag)
                                                <span>{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="menu-empty">
                                Belum ada menu aktif saat ini. Owner bisa menambahkan menu dari Dashboard Owner ke halaman Katalog Menu.
                            </div>
                        @endforelse
                    </div>
                </div>
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
    <script src="{{ $assetPath('js/tracker.js') }}"></script>
</body>
</html>


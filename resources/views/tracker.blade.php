<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $assetBase = rtrim(request()->getBasePath(), '/');
        $assetPath = static fn (string $path): string => ($assetBase !== '' ? $assetBase : '').'/'.ltrim($path, '/');
        $isDriver = auth()->check() && auth()->user()->isDriver();
        $driverAttendanceState = 'guest';
        $driverAttendanceLabel = 'Login sebagai driver untuk melihat status absensi.';

        $heroCustomPath = public_path('images/hero-user.jpg');
        $heroBannerImage = file_exists($heroCustomPath)
            ? $assetPath('images/hero-user.jpg').'?v='.filemtime($heroCustomPath)
            : $assetPath('images/coffee-hero-banner-new.png');
        $menuCatalogForPopup = $menuCatalog->map(static function ($menu): array {
            return [
                'name' => (string) $menu->name,
                'price' => (int) $menu->price,
                'category' => (string) $menu->category,
            ];
        })->values();

        if ($isDriver) {
            $driverAccount = auth()->user()->loadMissing('activeDriverAssignment');
            $activeAssignment = $driverAccount->activeDriverAssignment;

            if (! $activeAssignment) {
                $driverAttendanceState = 'no_assignment';
                $driverAttendanceLabel = 'Belum ada assignment aktif.';
            } elseif ($activeAssignment->checked_in_at !== null && $activeAssignment->checked_out_at === null) {
                $driverAttendanceState = 'clocked_in';
                $driverAttendanceLabel = 'Sudah absen masuk.';
            } elseif ($activeAssignment->checked_in_at !== null && $activeAssignment->checked_out_at !== null) {
                $driverAttendanceState = 'clocked_out';
                $driverAttendanceLabel = 'Sudah absen keluar.';
            } else {
                $driverAttendanceState = 'not_clocked_in';
                $driverAttendanceLabel = 'Belum absen masuk.';
            }
        }
    @endphp
    <title>FindMyCoffee</title>
    <link rel="icon" type="image/png" sizes="512x512" href="{{ $assetPath('favicon.png') }}">
    <link rel="shortcut icon" href="{{ $assetPath('favicon.ico') }}">
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
    data-driver-qr-enabled="{{ $isDriver ? '1' : '0' }}"
    data-driver-attendance-state="{{ $driverAttendanceState }}"
    data-driver-attendance-label="{{ $driverAttendanceLabel }}"
>
    <div class="tracker-shell">
        <header class="tracker-topbar">
            <div class="tracker-brand">
                <img
                    class="tracker-brand-mark"
                    src="{{ $assetPath('images/ada-coffee-logo.png') }}"
                    alt="FindMyCoffee"
                >
                <div class="tracker-brand-copy">
                    <strong><span class="brand-wordmark">FindMy<span class="brand-wordmark-accent">Coffee</span></span></strong>
                    <span>Cara termudah mencari aroma kopi favorit yang sedang berpindah.</span>
                </div>
            </div>

            <div class="tracker-nav-controls">
                @if ($isDriver)
                    <button
                        type="button"
                        class="tracker-scan-toggle"
                        data-driver-qr-open
                        aria-label="Buka scanner QR driver"
                    >
                        <img src="{{ $assetPath('images/qrscan.png') }}" alt="" aria-hidden="true">
                        <span class="tracker-sr-only">Scan QR</span>
                    </button>
                @endif

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
            </div>

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
                        <span class="eyebrow"><span class="brand-wordmark">FindMy<span class="brand-wordmark-accent">Coffee</span></span></span>
                        <h1>Kopling terdekat langsung terlihat.</h1>
                        <p>
                            Jangan biarkan antrean atau jarak menghalangi kafeinmu. Dari gang sempit hingga jalan
                            protokol, kami petakan setiap gerobak agar kamu bisa pesan tanpa sasar.
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
                        Kami percaya bahwa kopi enak tidak harus selalu ada di dalam gedung mewah. Bermula dari rasa
                        penasaran mencari gerobak kopi langganan yang sering berpindah tempat, website ini lahir untuk
                        mendukung ekosistem kopi jalanan. Kami hadir untuk memastikan tidak ada lagi pecinta kopi yang
                        kehilangan jejak barista favoritnya. Kami menghubungkan teknologi dengan tradisi kopi keliling
                        agar kamu bisa menikmati segelas inspirasi tepat di trotoar jalanan kota.
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
                            Dari racikan klasik hingga kreasi unik, jelajahi pilihan menu terbaik yang siap menemani
                            perjalananmu hari ini.
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

            <footer class="tracker-contact-footer">
                <div class="tracker-contact-inner">
                    <section class="tracker-contact-brand">
                        <span class="eyebrow">Kontak Info</span>
                        <h3><span class="brand-wordmark">FindMy<span class="brand-wordmark-accent">Coffee</span></span></h3>
                        <p>
                            Kopi keliling dengan tracker realtime. Hubungi kami untuk kolaborasi event,
                            pemesanan rombongan, atau info titik jual terdekat.
                        </p>
                    </section>

                    <section>
                        <h4>Hubungi Kami</h4>
                        <ul class="tracker-contact-list">
                            <li><strong>WhatsApp:</strong> <a href="https://wa.me/6282353151402" target="_blank" rel="noopener">+62 823-531-514-02</a></li>
                            <li><strong>Email:</strong> <a href="mailto:hello@adacoffee.id">hello@adacoffee.id</a></li>
                            <li><strong>Instagram:</strong> <a href="https://instagram.com/adacoffee.id" target="_blank" rel="noopener">@adacoffee.id</a></li>
                            <li><strong>Alamat:</strong> Samarinda, Kalimantan Timur</li>
                        </ul>
                    </section>

                    <section>
                        <h4>Navigasi Cepat</h4>
                        <ul class="tracker-footer-links">
                            <li><a href="#beranda">Beranda</a></li>
                            <li><a href="#lacak">Lacak Kopling</a></li>
                            <li><a href="#menu">Katalog Menu</a></li>
                            <li><a href="#tentang">About Us</a></li>
                        </ul>
                    </section>
                </div>

                <div class="tracker-contact-bottom">
                    <span>&copy; {{ now()->year }} <span class="brand-wordmark">FindMy<span class="brand-wordmark-accent">Coffee</span></span>. Seluruh hak cipta dilindungi.</span>
                </div>
            </footer>
        </main>
    </div>

    @if ($isDriver)
        <div class="driver-qr-modal" id="driver-qr-modal" hidden>
            <div class="driver-qr-dialog" role="dialog" aria-modal="true" aria-labelledby="driver-qr-title">
                <div class="driver-qr-header">
                    <div>
                        <p class="driver-qr-kicker">Driver Tools</p>
                        <h3 id="driver-qr-title">Scan QR di landing page</h3>
                    </div>
                    <button type="button" class="driver-qr-close" id="driver-qr-close" aria-label="Tutup scanner">&times;</button>
                </div>

                <div class="driver-qr-attendance" id="driver-qr-attendance" data-state="{{ $driverAttendanceState }}">
                    <span>Status absen sekarang</span>
                    <strong id="driver-qr-attendance-value">{{ $driverAttendanceLabel }}</strong>
                </div>

                <div id="driver-qr-reader" class="driver-qr-reader"></div>
                <p id="driver-qr-status" class="driver-qr-status">Arahkan kamera ke QR code untuk membaca data.</p>
            </div>
        </div>
    @endif

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>
    <script id="menu-catalog-json" type="application/json">
        @json($menuCatalogForPopup)
    </script>
    @if ($isDriver)
        <script src="https://unpkg.com/html5-qrcode"></script>
    @endif
    <script src="{{ $assetPath('js/tracker.js') }}"></script>
</body>
</html>

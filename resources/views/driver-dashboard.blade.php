<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/driver-dashboard.css') }}">
@endpush

    <x-slot name="header">
        <div class="driver-header">
            <div>
                <p class="driver-header-kicker">Dashboard Driver</p>
                <h2 class="driver-header-title">Panel tugas dan GPS harian</h2>
                <p class="driver-header-subtitle">Pantau status assignment, kondisi device tracker, dan riwayat aktivitas kamu dari satu halaman.</p>
            </div>

            <span class="driver-header-badge {{ $isClockedIn ? 'is-active' : ($assignment ? 'is-waiting' : 'is-idle') }}">
                {{ $isClockedIn ? 'Sudah absen masuk' : ($assignment ? 'Belum absen masuk' : 'Menunggu assignment') }}
            </span>
        </div>
    </x-slot>

    <div class="driver-page">
        @if (session('dashboard_status'))
            <section class="driver-alert">
                {{ session('dashboard_status') }}
            </section>
        @endif

        <section class="driver-overview-grid">
            <article class="driver-overview-card">
                <span class="driver-overview-label">Driver</span>
                <strong>{{ $driver->name }}</strong>
                <p>{{ $driver->email }}</p>
            </article>

            <article class="driver-overview-card">
                <span class="driver-overview-label">Unit Aktif</span>
                <strong>{{ $unit?->name ?? 'Belum ada' }}</strong>
                <p>{{ $unit?->code ? 'Kode '.$unit->code : 'Hubungi owner untuk penugasan.' }}</p>
            </article>

            <article class="driver-overview-card">
                <span class="driver-overview-label">Durasi Shift</span>
                <strong>
                    @if ($shiftDurationMinutes !== null)
                        {{ floor($shiftDurationMinutes / 60) }}j {{ $shiftDurationMinutes % 60 }}m
                    @else
                        -
                    @endif
                </strong>
                <p>{{ $shiftStartAt?->translatedFormat('d M Y H:i') ? 'Mulai '.$shiftStartAt->translatedFormat('d M Y H:i') : 'Belum mulai karena belum absen masuk.' }}</p>
            </article>

            <article class="driver-overview-card">
                <span class="driver-overview-label">GPS Terakhir</span>
                <strong class="gps-status {{ $isLocationFresh ? 'is-fresh' : 'is-stale' }}">
                    {{ $latestLocation?->recorded_at?->diffForHumans() ?? 'Belum ada data' }}
                </strong>
                <p>{{ $latestLocation?->battery_level !== null ? 'Baterai '.$latestLocation->battery_level.'%' : 'Belum ada data baterai.' }}</p>
            </article>
        </section>

        <section class="driver-main-grid">
            <article class="driver-panel">
                <div class="driver-panel-head">
                    <div>
                        <span class="driver-panel-kicker">Status Tugas Hari Ini</span>
                        <h3>Detail assignment aktif</h3>
                    </div>
                    <a href="{{ route('tracker.index') }}" class="driver-ghost-link">Buka peta publik</a>
                </div>

                @if ($assignment && $unit)
                    @php
                        $mapsUrl = $latestLocation
                            ? 'https://www.google.com/maps?q='.$latestLocation->latitude.','.$latestLocation->longitude
                            : null;
                    @endphp

                    <div class="driver-attendance-box {{ $isClockedIn ? 'is-clocked-in' : ($isClockedOut ? 'is-clocked-out' : 'is-pending') }}">
                        <div>
                            <span>Status absensi</span>
                            <strong>
                                @if ($isClockedIn)
                                    Sudah absen masuk
                                @elseif ($isClockedOut)
                                    Sudah absen keluar
                                @else
                                    Belum absen masuk
                                @endif
                            </strong>
                            <p>Lokasi GPS hanya muncul di peta publik jika kamu sudah absen masuk.</p>
                        </div>
                    </div>

                    <div class="driver-info-grid">
                        <div class="driver-info-item">
                            <span>Nama unit</span>
                            <strong>{{ $unit->name }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <span>Kode unit</span>
                            <strong>{{ $unit->code }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <span>Device tracker</span>
                            <strong>{{ $driver->device_id ?? '-' }}</strong>
                        </div>
                        <div class="driver-info-item">
                            <span>Di-assign oleh</span>
                            <strong>{{ $assignment->assignedBy?->name ?? 'Owner' }}</strong>
                        </div>
                    </div>

                    <div class="driver-location-box">
                        <div class="driver-location-row">
                            <span>Latitude</span>
                            <strong>{{ $latestLocation?->latitude ?? '-' }}</strong>
                        </div>
                        <div class="driver-location-row">
                            <span>Longitude</span>
                            <strong>{{ $latestLocation?->longitude ?? '-' }}</strong>
                        </div>
                        <div class="driver-location-row">
                            <span>Akurasi</span>
                            <strong>{{ $latestLocation?->accuracy !== null ? $latestLocation->accuracy.' m' : '-' }}</strong>
                        </div>
                        <div class="driver-location-row">
                            <span>Status gerak</span>
                            <strong>
                                @if ($latestLocation?->is_moving === null)
                                    -
                                @else
                                    {{ $latestLocation->is_moving ? 'Bergerak' : 'Diam' }}
                                @endif
                            </strong>
                        </div>
                    </div>

                    @if ($mapsUrl)
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="driver-primary-link">Lihat titik terbaru di Google Maps</a>
                    @endif
                @else
                    <div class="driver-empty-state">
                        Belum ada assignment aktif. Pastikan akun driver ini sudah di-assign ke unit dari dashboard owner.
                    </div>
                @endif
            </article>

            <article class="driver-panel">
                <div class="driver-panel-head">
                    <div>
                        <span class="driver-panel-kicker">Checklist Operasional</span>
                        <h3>Hal yang perlu dicek</h3>
                    </div>
                </div>

                <ul class="driver-checklist">
                    <li class="{{ $assignment ? 'is-done' : 'is-pending' }}">
                        <strong>Assignment aktif</strong>
                        <span>{{ $assignment ? 'Sudah ada penugasan unit hari ini.' : 'Belum ada assignment aktif.' }}</span>
                    </li>
                    <li class="{{ $isClockedIn ? 'is-done' : 'is-pending' }}">
                        <strong>Absensi masuk</strong>
                        <span>{{ $isClockedIn ? 'Sudah absen masuk, lokasi tampil di peta publik.' : 'Belum absen masuk, lokasi disembunyikan dari peta publik.' }}</span>
                    </li>
                    <li class="{{ ! blank($driver->device_id) ? 'is-done' : 'is-pending' }}">
                        <strong>Device ID</strong>
                        <span>{{ ! blank($driver->device_id) ? 'Device ID sudah tersimpan: '.$driver->device_id : 'Device ID belum diisi.' }}</span>
                    </li>
                    <li class="{{ $latestLocation ? 'is-done' : 'is-pending' }}">
                        <strong>Sinkronisasi GPS</strong>
                        <span>{{ $latestLocation ? 'Lokasi sudah pernah terkirim ke server.' : 'Belum ada data lokasi masuk.' }}</span>
                    </li>
                    <li class="{{ $isLocationFresh ? 'is-done' : 'is-pending' }}">
                        <strong>Kebaruan lokasi</strong>
                        <span>{{ $isLocationFresh ? 'Update lokasi terbaru masih fresh (<= 5 menit).' : 'Update lokasi lebih dari 5 menit atau belum tersedia.' }}</span>
                    </li>
                </ul>
            </article>
        </section>

        <section class="driver-bottom-grid">
            <article class="driver-panel">
                <div class="driver-panel-head">
                    <div>
                        <span class="driver-panel-kicker">Riwayat Absensi</span>
                        <h3>5 absen masuk terakhir</h3>
                    </div>
                </div>

                <div class="driver-timeline">
                    @forelse ($recentAttendanceLogs as $item)
                        <article class="driver-timeline-item">
                            <strong>{{ $item->unit_name ?? $item->assignment?->unit?->name ?? 'Unit tidak ditemukan' }}</strong>
                            <span>
                                {{ $item->clocked_in_at?->translatedFormat('d M Y H:i') }}
                                &rarr;
                                {{ $item->clocked_out_at?->translatedFormat('d M Y H:i') ?? 'Masih aktif' }}
                            </span>
                        </article>
                    @empty
                        <div class="driver-empty-state">Belum ada riwayat absensi masuk.</div>
                    @endforelse
                </div>
            </article>

            <article class="driver-panel">
                <div class="driver-panel-head">
                    <div>
                        <span class="driver-panel-kicker">Riwayat Lokasi</span>
                        <h3>5 data GPS terbaru</h3>
                    </div>
                </div>

                <div class="driver-timeline">
                    @forelse ($recentLocations as $location)
                        <article class="driver-timeline-item">
                            <strong>{{ $location->recorded_at?->translatedFormat('d M Y H:i:s') }}</strong>
                            <span>
                                {{ $location->latitude }}, {{ $location->longitude }}
                                | Bat: {{ $location->battery_level !== null ? $location->battery_level.'%' : '-' }}
                            </span>
                        </article>
                    @empty
                        <div class="driver-empty-state">Belum ada data lokasi dari device ini.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
</x-app-layout>

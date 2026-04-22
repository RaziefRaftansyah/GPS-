<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/driver-dashboard.css') }}">
@endpush

    <x-slot name="header">
        <div>
            <p class="page-header-kicker">
                Dashboard Driver
            </p>
            <h2 class="page-header-title">
                Lihat gerobak tugasmu hari ini
            </h2>
        </div>
    </x-slot>

    <div class="driver-dashboard">
        <section class="driver-grid-top">
            <article class="driver-card">
                <span class="driver-label">Driver</span>
                <strong class="driver-strong-lg">{{ $driver->name }}</strong>
                <span class="driver-muted">{{ $driver->email }}</span>
            </article>
            <article class="driver-card">
                <span class="driver-label">Gerobak Aktif</span>
                <strong class="driver-strong-lg">{{ $unit?->name ?? 'Belum ada assignment' }}</strong>
                <span class="driver-muted">{{ $unit?->code ? 'Kode '.$unit->code : 'Hubungi owner untuk penugasan.' }}</span>
            </article>
            <article class="driver-card">
                <span class="driver-label">Device Tracker</span>
                <strong class="driver-strong-md">{{ $driver->device_id ?? '-' }}</strong>
                <span class="driver-muted">Gunakan `device_id` HP ini di aplikasi Traccar.</span>
            </article>
        </section>

        <section class="driver-grid-main">
            <article class="driver-card">
                <span class="driver-label">Status Tugas</span>
                <h3 class="driver-title">{{ $assignment ? 'Kamu sedang bertugas.' : 'Belum ada penugasan aktif.' }}</h3>

                @if ($assignment && $unit)
                    <div class="driver-stat-grid">
                        <div class="driver-stat">
                            <span>Gerobak</span>
                            <strong>{{ $unit->name }}</strong>
                        </div>
                        <div class="driver-stat">
                            <span>Mulai Tugas</span>
                            <strong>{{ $assignment->assigned_at?->translatedFormat('d M Y H:i') }}</strong>
                        </div>
                        <div class="driver-stat">
                            <span>Lokasi Terakhir</span>
                            <strong>{{ $latestLocation?->recorded_at?->diffForHumans() ?? 'Belum ada data' }}</strong>
                        </div>
                        <div class="driver-stat">
                            <span>Baterai</span>
                            <strong>{{ $latestLocation?->battery_level !== null ? $latestLocation->battery_level.'%' : '-' }}</strong>
                        </div>
                    </div>
                @else
                    <div class="driver-empty driver-empty-gap">
                        Owner belum assign gerobak ke akunmu. Begitu assignment dibuat, detail gerobak dan device tracker akan muncul di sini.
                    </div>
                @endif
            </article>

            <article class="driver-card">
                <span class="driver-label">Riwayat Tugas</span>
                <h3 class="driver-subtitle">5 assignment terakhir</h3>
                <div class="driver-list">
                    @forelse ($recentAssignments as $item)
                        <article>
                            <strong>{{ $item->unit?->name ?? 'Gerobak tidak ditemukan' }}</strong>
                            <span class="driver-muted driver-list-meta">
                                {{ $item->assigned_at?->translatedFormat('d M Y H:i') }} - {{ $item->ended_at?->translatedFormat('d M Y H:i') ?? 'Masih aktif' }}
                            </span>
                        </article>
                    @empty
                        <div class="driver-empty">Belum ada riwayat assignment.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
</x-app-layout>

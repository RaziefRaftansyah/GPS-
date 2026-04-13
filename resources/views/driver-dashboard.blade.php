<x-app-layout>
    <style>
        .driver-dashboard {
            display: grid;
            gap: 24px;
        }

        .driver-grid-top,
        .driver-grid-main {
            display: grid;
            gap: 20px;
        }

        .driver-grid-top {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .driver-grid-main {
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
        }

        .driver-card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            padding: 24px;
        }

        .driver-label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-soft);
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .driver-card strong {
            display: block;
        }

        .driver-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .driver-stat {
            border-radius: 16px;
            padding: 16px;
            border: 1px solid var(--panel-border);
            background: #f8fafc;
        }

        .driver-stat span {
            display: block;
            color: var(--text-soft);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .driver-stat strong {
            margin-top: 8px;
        }

        .driver-empty {
            padding: 18px;
            border-radius: 16px;
            border: 1px dashed var(--panel-border);
            background: #f8fafc;
            color: var(--text-soft);
        }

        .driver-list {
            display: grid;
            gap: 12px;
        }

        .driver-list article {
            padding: 16px 18px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--panel-border);
        }

        @media (max-width: 1180px) {
            .driver-grid-top,
            .driver-grid-main,
            .driver-stat-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <x-slot name="header">
        <div>
            <p style="margin: 0; color: var(--text-soft); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                Dashboard Driver
            </p>
            <h2 style="margin: 8px 0 0; font-size: 2rem; color: var(--text-main);">
                Lihat gerobak tugasmu hari ini
            </h2>
        </div>
    </x-slot>

    <div class="driver-dashboard">
        <section class="driver-grid-top">
            <article class="driver-card">
                <span class="driver-label">Driver</span>
                <strong style="font-size: 1.6rem;">{{ $driver->name }}</strong>
                <span style="display: block; margin-top: 8px; color: var(--text-soft);">{{ $driver->email }}</span>
            </article>
            <article class="driver-card">
                <span class="driver-label">Gerobak Aktif</span>
                <strong style="font-size: 1.6rem;">{{ $unit?->name ?? 'Belum ada assignment' }}</strong>
                <span style="display: block; margin-top: 8px; color: var(--text-soft);">{{ $unit?->code ? 'Kode '.$unit->code : 'Hubungi owner untuk penugasan.' }}</span>
            </article>
            <article class="driver-card">
                <span class="driver-label">Device Tracker</span>
                <strong style="font-size: 1.4rem;">{{ $driver->device_id ?? '-' }}</strong>
                <span style="display: block; margin-top: 8px; color: var(--text-soft);">Gunakan `device_id` HP ini di aplikasi Traccar.</span>
            </article>
        </section>

        <section class="driver-grid-main">
            <article class="driver-card">
                <span class="driver-label">Status Tugas</span>
                <h3 style="margin: 0; font-size: 1.7rem;">{{ $assignment ? 'Kamu sedang bertugas.' : 'Belum ada penugasan aktif.' }}</h3>

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
                    <div class="driver-empty" style="margin-top: 18px;">
                        Owner belum assign gerobak ke akunmu. Begitu assignment dibuat, detail gerobak dan device tracker akan muncul di sini.
                    </div>
                @endif
            </article>

            <article class="driver-card">
                <span class="driver-label">Riwayat Tugas</span>
                <h3 style="margin: 0 0 16px; font-size: 1.5rem;">5 assignment terakhir</h3>
                <div class="driver-list">
                    @forelse ($recentAssignments as $item)
                        <article>
                            <strong>{{ $item->unit?->name ?? 'Gerobak tidak ditemukan' }}</strong>
                            <span style="display: block; margin-top: 6px; color: var(--text-soft);">
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

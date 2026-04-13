<x-app-layout>
    <style>
        .owner-dashboard {
            display: grid;
            gap: 24px;
        }

        .owner-grid-4,
        .owner-grid-3,
        .owner-grid-main {
            display: grid;
            gap: 20px;
        }

        .owner-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .owner-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .owner-grid-main {
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.85fr);
        }

        .panel-card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }

        .metric-card,
        .section-card,
        .mini-card {
            padding: 24px;
        }

        .eyebrow {
            display: block;
            margin-bottom: 10px;
            color: var(--text-soft);
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .metric-card strong {
            display: block;
            font-size: 2.2rem;
            line-height: 1;
        }

        .metric-card p,
        .section-subtext,
        .unit-meta,
        .list-meta {
            color: var(--text-soft);
            line-height: 1.6;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .section-heading h3,
        .form-card h3 {
            margin: 0;
            font-size: 1.6rem;
        }

        .section-button,
        .primary-button,
        .success-button,
        .ghost-button,
        .danger-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .section-button,
        .ghost-button {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--panel-border);
        }

        .primary-button {
            background: var(--accent);
            color: #fff;
        }

        .success-button {
            background: var(--success);
            color: #fff;
        }

        .danger-button {
            background: rgba(220, 38, 38, 0.12);
            color: var(--danger);
        }

        .form-stack {
            display: grid;
            gap: 12px;
        }

        .dashboard-input,
        .dashboard-select,
        .dashboard-textarea {
            width: 100%;
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            padding: 13px 14px;
            font: inherit;
            color: var(--text-main);
            background: #fff;
        }

        .dashboard-textarea {
            min-height: 96px;
            resize: vertical;
        }

        .unit-list,
        .mini-list {
            display: grid;
            gap: 14px;
        }

        .unit-item,
        .mini-item {
            border: 1px solid var(--panel-border);
            border-radius: 18px;
            background: #fff;
        }

        .unit-item {
            padding: 18px;
        }

        .mini-item {
            padding: 16px 18px;
        }

        .unit-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
            flex-wrap: wrap;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700;
            font-size: 0.82rem;
        }

        .unit-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .unit-stat {
            border-radius: 14px;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid var(--panel-border);
        }

        .unit-stat span {
            display: block;
            color: var(--text-soft);
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .unit-stat strong {
            display: block;
            margin-top: 8px;
        }

        .status-banner {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(22, 163, 74, 0.12);
            border: 1px solid rgba(22, 163, 74, 0.16);
            color: #166534;
        }

        .empty-state {
            padding: 18px;
            border-radius: 16px;
            border: 1px dashed var(--panel-border);
            color: var(--text-soft);
            background: #f8fafc;
        }

        @media (max-width: 1180px) {
            .owner-grid-4,
            .owner-grid-3,
            .owner-grid-main,
            .unit-stats {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div>
                <p style="margin: 0; color: var(--text-soft); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700;">
                    Dashboard Admin
                </p>
                <h2 style="margin: 8px 0 0; font-size: 2rem; color: var(--text-main);">
                    Pantau user yang sedang login
                </h2>
                <p style="margin: 8px 0 0; color: var(--text-soft);">
                    Kelola driver, gerobak, assignment, dan aktivitas operasional dari satu dashboard.
                </p>
            </div>
            <a href="{{ route('dashboard.traccar') }}" class="section-button">
                Monitoring Traccar
            </a>
        </div>
    </x-slot>

    <div class="owner-dashboard">
        @if (session('dashboard_status'))
            <section class="status-banner">
                {{ session('dashboard_status') }}
            </section>
        @endif

        <section class="owner-grid-4">
            <article class="panel-card metric-card">
                <span class="eyebrow">User Aktif</span>
                <strong>{{ $activeUserCount }}</strong>
                <p>{{ $activeUserCount }} user online</p>
            </article>
            <article class="panel-card metric-card">
                <span class="eyebrow">Gerobak</span>
                <strong>{{ $unitCount }}</strong>
                <p>{{ $assignedUnitCount }} gerobak sedang terhubung dengan driver aktif.</p>
            </article>
            <article class="panel-card metric-card">
                <span class="eyebrow">Driver</span>
                <strong>{{ $driverCount }}</strong>
                <p>{{ $availableDrivers->count() }} driver tersedia untuk di-assign.</p>
            </article>
            <article class="panel-card metric-card">
                <span class="eyebrow">Owner Utama</span>
                <strong style="font-size: 1.25rem; line-height: 1.35;">{{ $latestLoginAt?->translatedFormat('d M Y H:i') ?? 'Belum ada data' }}</strong>
                <p>{{ $adminEmail }}</p>
            </article>
        </section>

        <section class="owner-grid-3">
            <article class="panel-card section-card form-card">
                <span class="eyebrow">Data Unit</span>
                <h3>Tambah gerobak</h3>
                <p class="section-subtext">Buat master gerobak baru. GPS akan dibaca dari HP driver yang sedang ditugaskan.</p>
                <form method="POST" action="{{ route('dashboard.units.store') }}" class="form-stack">
                    @csrf
                    <input class="dashboard-input" type="text" name="name" placeholder="Nama gerobak" value="{{ old('name') }}">
                    <input class="dashboard-input" type="text" name="code" placeholder="Kode unit, contoh GRBK-01" value="{{ old('code') }}">
                    <select class="dashboard-select" name="status">
                        <option value="ready">Siap Operasi</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                    <textarea class="dashboard-textarea" name="notes" placeholder="Catatan unit">{{ old('notes') }}</textarea>
                    <button type="submit" class="primary-button">Simpan Gerobak</button>
                </form>
            </article>

            <article class="panel-card section-card form-card">
                <span class="eyebrow">Data Driver</span>
                <h3>Buat akun driver</h3>
                <p class="section-subtext">Akun ini dipakai driver untuk login dan mengirim GPS dari HP sesuai `device_id` miliknya.</p>
                <form method="POST" action="{{ route('dashboard.drivers.store') }}" class="form-stack">
                    @csrf
                    <input class="dashboard-input" type="text" name="name" placeholder="Nama driver">
                    <input class="dashboard-input" type="email" name="email" placeholder="Email driver">
                    <input class="dashboard-input" type="text" name="device_id" placeholder="Device ID HP driver">
                    <input class="dashboard-input" type="password" name="password" placeholder="Password minimal 8 karakter">
                    <button type="submit" class="primary-button">Buat Akun Driver</button>
                </form>
            </article>

            <article class="panel-card section-card form-card">
                <span class="eyebrow">Assignment</span>
                <h3>Assign driver ke gerobak</h3>
                <p class="section-subtext">Satu driver aktif untuk satu gerobak aktif. Assignment lama akan ditutup otomatis.</p>
                <form method="POST" action="{{ route('dashboard.assignments.store') }}" class="form-stack">
                    @csrf
                    <select class="dashboard-select" name="driver_id">
                        <option value="">Pilih driver</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }} - {{ $driver->email }}</option>
                        @endforeach
                    </select>
                    <select class="dashboard-select" name="unit_id">
                        <option value="">Pilih gerobak</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit['id'] }}">{{ $unit['name'] }} ({{ $unit['code'] }})</option>
                        @endforeach
                    </select>
                    <textarea class="dashboard-textarea" name="notes" placeholder="Catatan assignment"></textarea>
                    <button type="submit" class="success-button">Assign Sekarang</button>
                </form>
            </article>
        </section>

        <section class="owner-grid-main">
            <article class="panel-card section-card">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Operasional</span>
                        <h3>Status gerobak dan driver aktif</h3>
                        <p class="section-subtext">Pantau gerobak mana yang sudah berjalan, siapa drivernya, dan update lokasi terakhir.</p>
                    </div>
                </div>

                <div class="unit-list">
                    @forelse ($units as $unit)
                        <article class="unit-item">
                            <div class="unit-head">
                                <div>
                                    <strong style="display: block; font-size: 1.08rem;">{{ $unit['name'] }}</strong>
                                    <span class="unit-meta" style="display: block; margin-top: 6px;">{{ $unit['code'] }} • {{ $unit['device_id'] ? 'HP '.$unit['device_id'] : 'Belum ada HP driver aktif' }}</span>
                                </div>
                                <span class="status-pill">{{ ucfirst($unit['status']) }}</span>
                            </div>

                            <div class="unit-stats">
                                <div class="unit-stat">
                                    <span>Driver Aktif</span>
                                    <strong>{{ $unit['active_assignment']?->driver?->name ?? 'Belum ada driver' }}</strong>
                                </div>
                                <div class="unit-stat">
                                    <span>Update Lokasi</span>
                                    <strong>{{ $unit['latest_location']?->recorded_at?->diffForHumans() ?? 'Belum ada data' }}</strong>
                                </div>
                                <div class="unit-stat">
                                    <span>Baterai</span>
                                    <strong>{{ $unit['latest_location']?->battery_level !== null ? $unit['latest_location']->battery_level.'%' : '-' }}</strong>
                                </div>
                            </div>

                            @if ($unit['active_assignment'])
                                <form method="POST" action="{{ route('dashboard.assignments.finish', $unit['active_assignment']) }}" style="margin-top: 14px;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="danger-button">Selesaikan Assignment</button>
                                </form>
                            @endif
                        </article>
                    @empty
                        <div class="empty-state">Belum ada gerobak terdaftar.</div>
                    @endforelse
                </div>
            </article>

            <div style="display: grid; gap: 20px;">
                <article class="panel-card mini-card">
                    <span class="eyebrow">Assignment Aktif</span>
                    <h3 style="margin: 0 0 16px; font-size: 1.5rem;">Siapa bawa gerobak mana</h3>
                    <div class="mini-list">
                        @forelse ($activeAssignments as $assignment)
                            <article class="mini-item">
                                <strong style="display: block;">{{ $assignment->driver?->name }} → {{ $assignment->unit?->name }}</strong>
                                <span class="list-meta" style="display: block; margin-top: 6px;">Mulai {{ $assignment->assigned_at?->translatedFormat('d M Y H:i') }}</span>
                            </article>
                        @empty
                            <div class="empty-state">Belum ada assignment aktif.</div>
                        @endforelse
                    </div>
                </article>

                <article class="panel-card mini-card">
                    <span class="eyebrow">User Login</span>
                    <h3 style="margin: 0 0 16px; font-size: 1.5rem;">Daftar user yang sedang aktif</h3>
                    <div class="mini-list">
                        @forelse ($activeUsers as $activeUser)
                            <article class="mini-item">
                                <div style="display: flex; justify-content: space-between; gap: 12px; align-items: start; flex-wrap: wrap;">
                                    <div>
                                        <strong style="display: block;">{{ $activeUser['name'] }}</strong>
                                        <span class="list-meta" style="display: block; margin-top: 6px;">{{ $activeUser['email'] }}</span>
                                        <span class="list-meta" style="display: block; margin-top: 6px;">{{ $activeUser['last_seen']->diffForHumans() }}</span>
                                    </div>
                                    @if (! $activeUser['is_current_admin'])
                                        <form method="POST" action="{{ route('dashboard.users.kick', $activeUser['user_id']) }}" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="danger-button">Kick</button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="empty-state">Belum ada user aktif.</div>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>
    </div>
</x-app-layout>

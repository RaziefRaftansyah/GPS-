<x-app-layout>
    <style>
        .owner-dashboard {
            display: grid;
            gap: 22px;
        }

        .owner-grid-4,
        .owner-grid-3,
        .owner-grid-main {
            display: grid;
            gap: 18px;
        }

        .owner-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .owner-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .owner-grid-main {
            grid-template-columns: minmax(0, 1.38fr) minmax(320px, 0.86fr);
            align-items: start;
        }

        .panel-card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 26px;
            box-shadow: var(--shadow-sm);
        }

        .metric-card,
        .section-card,
        .mini-card {
            padding: 24px;
        }

        .metric-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        }

        .metric-card strong {
            display: block;
            font-size: 2.2rem;
            line-height: 1;
            margin-top: 10px;
            color: var(--espresso);
        }

        .metric-card p,
        .section-subtext,
        .unit-meta,
        .list-meta {
            color: var(--text-soft);
            line-height: 1.65;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(106, 65, 45, 0.7);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
        }

        .eyebrow::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--caramel);
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
            margin: 8px 0 0;
            font-size: 1.55rem;
            color: var(--espresso);
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
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .section-button:hover,
        .primary-button:hover,
        .success-button:hover,
        .ghost-button:hover,
        .danger-button:hover {
            transform: translateY(-1px);
        }

        .section-button,
        .ghost-button {
            background: rgba(255, 255, 255, 0.8);
            color: var(--accent);
            border: 1px solid rgba(45, 99, 226, 0.18);
            box-shadow: 0 12px 28px rgba(59, 36, 24, 0.08);
        }

        .primary-button {
            background: linear-gradient(135deg, var(--mocha) 0%, var(--caramel) 100%);
            color: #fff;
            box-shadow: 0 16px 28px rgba(106, 65, 45, 0.24);
        }

        .success-button {
            background: linear-gradient(135deg, #2f6b55 0%, #4d8b73 100%);
            color: #fff;
            box-shadow: 0 16px 28px rgba(47, 107, 85, 0.24);
        }

        .danger-button {
            background: rgba(239, 91, 122, 0.12);
            color: var(--danger);
        }

        .action-card {
            display: grid;
            gap: 16px;
            align-content: start;
            background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
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
            border-radius: 16px;
            padding: 14px 16px;
            font: inherit;
            color: var(--text-main);
            background: var(--panel-alt);
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
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        }

        .unit-item {
            padding: 20px;
        }

        .mini-item {
            padding: 18px 18px;
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
            padding: 8px 14px;
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
            margin-top: 16px;
        }

        .unit-stat {
            border-radius: 18px;
            padding: 15px;
            background: rgba(255, 249, 241, 0.84);
            border: 1px solid var(--panel-border);
        }

        .unit-stat span {
            display: block;
            color: rgba(106, 65, 45, 0.64);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .unit-stat strong {
            display: block;
            margin-top: 8px;
            font-size: 1rem;
            line-height: 1.45;
        }

        .highlight-panel {
            padding: 26px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        }

        .highlight-grid {
            display: grid;
            grid-template-columns: minmax(280px, 0.88fr) minmax(0, 1fr);
            gap: 18px;
        }

        .profile-card {
            display: grid;
            align-content: start;
            justify-items: center;
            padding: 26px 18px;
            border-radius: 24px;
            background: var(--panel-alt);
            border: 1px solid var(--panel-border);
            text-align: center;
        }

        .profile-avatar {
            width: 132px;
            height: 132px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(106, 65, 45, 0.16) 0%, rgba(181, 106, 59, 0.18) 100%);
            color: var(--mocha);
            font-size: 2.6rem;
            font-weight: 700;
            box-shadow: inset 0 0 0 10px #fff;
        }

        .profile-card strong {
            display: block;
            margin-top: 18px;
            font-size: 1.55rem;
            line-height: 1.2;
            color: var(--espresso);
        }

        .profile-card span {
            display: block;
            margin-top: 6px;
        }

        .info-panel {
            display: grid;
            gap: 16px;
        }

        .info-card {
            padding: 20px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid var(--panel-border);
        }

        .info-card h4 {
            margin: 0 0 16px;
            font-size: 1.34rem;
            color: var(--espresso);
        }

        .info-grid {
            display: grid;
            gap: 12px;
        }

        .info-row {
            display: grid;
            grid-template-columns: minmax(120px, 0.8fr) minmax(0, 1fr);
            gap: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--panel-border);
        }

        .info-row:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .info-row span {
            color: #98a3c1;
            font-size: 0.92rem;
        }

        .info-row strong {
            font-size: 0.98rem;
            line-height: 1.5;
            color: #2b315d;
        }

        .status-banner {
            padding: 16px 18px;
            border-radius: 20px;
            background: rgba(75, 201, 166, 0.14);
            border: 1px solid rgba(75, 201, 166, 0.2);
            color: #146c58;
        }

        .empty-state {
            padding: 18px;
            border-radius: 18px;
            border: 1px dashed var(--panel-border);
            color: var(--text-soft);
            background: rgba(255, 249, 241, 0.84);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(117, 127, 157, 0.32);
            backdrop-filter: blur(7px);
            z-index: 60;
        }

        .modal-overlay.is-open {
            display: flex;
        }

        .modal-card {
            width: min(560px, 100%);
            max-height: min(88vh, 820px);
            overflow: auto;
            padding: 24px;
            border-radius: 28px;
            background: #fff;
            border: 1px solid var(--panel-border);
            box-shadow: 0 30px 70px rgba(97, 115, 160, 0.22);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .modal-header h3 {
            margin: 8px 0 0;
            font-size: 1.7rem;
        }

        .modal-close {
            width: 40px;
            height: 40px;
            border: 1px solid var(--panel-border);
            border-radius: 14px;
            background: #fff;
            color: var(--text-main);
            cursor: pointer;
            font-size: 1.2rem;
        }

        @media (max-width: 1180px) {
            .owner-grid-4,
            .owner-grid-3,
            .owner-grid-main,
            .unit-stats,
            .highlight-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div>
                <p style="margin: 0; color: #96a1bf; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 700;">
                    Dashboard Admin
                </p>
                <h2 style="margin: 8px 0 0; font-size: 2.5rem; line-height: 1.08; color: #1c2350;">
                    Patient profile style untuk dashboard operasional
                </h2>
                <p style="margin: 10px 0 0; color: var(--text-soft); max-width: 760px;">
                    Tampilan disesuaikan mengikuti referensi klinik: sidebar lembut, kartu putih bersih, dan panel informasi yang lebih rapi tanpa mengubah fitur, isi, atau aksi dashboard.
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
                <p>{{ $activeUserCount }} user online sekarang.</p>
            </article>
            <article class="panel-card metric-card">
                <span class="eyebrow">Gerobak</span>
                <strong>{{ $unitCount }}</strong>
                <p>{{ $assignedUnitCount }} gerobak sedang terhubung dengan driver aktif.</p>
            </article>
            <article class="panel-card metric-card">
                <span class="eyebrow">Driver</span>
                <strong>{{ $driverCount }}</strong>
                <p>{{ $availableDrivers->count() }} driver masih tersedia untuk assignment baru.</p>
            </article>
            <article class="panel-card metric-card">
                <span class="eyebrow">Owner Utama</span>
                <strong style="font-size: 1.18rem; line-height: 1.4;">
                    {{ $latestLoginAt?->translatedFormat('d M Y H:i') ?? 'Belum ada data' }}
                </strong>
                <p>{{ $adminEmail }}</p>
            </article>
        </section>

        <section class="owner-grid-main">
            <article class="panel-card section-card">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Operasional</span>
                        <h3>Status gerobak dan driver aktif</h3>
                        <p class="section-subtext">Pantau gerobak mana yang berjalan, siapa drivernya, update lokasi terakhir, dan status baterai.</p>
                    </div>
                </div>

                <div class="unit-list">
                    @forelse ($units as $unit)
                        <article class="unit-item">
                            <div class="unit-head">
                                <div>
                                    <strong style="display: block; font-size: 1.08rem; color: var(--espresso);">{{ $unit['name'] }}</strong>
                                    <span class="unit-meta" style="display: block; margin-top: 6px;">
                                        {{ $unit['code'] }} • {{ $unit['device_id'] ? 'HP '.$unit['device_id'] : 'Belum ada HP driver aktif' }}
                                    </span>
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
                                <form method="POST" action="{{ route('dashboard.assignments.finish', $unit['active_assignment']) }}" style="margin-top: 16px;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="redirect_to" value="dashboard">
                                    <button type="submit" class="danger-button">Selesaikan Assignment</button>
                                </form>
                            @endif
                        </article>
                    @empty
                        <div class="empty-state">Belum ada gerobak terdaftar.</div>
                    @endforelse
                </div>
            </article>

            <div style="display: grid; gap: 18px;">
                <article class="panel-card mini-card">
                    <span class="eyebrow">Assignment Aktif</span>
                    <h3 style="margin: 10px 0 16px; font-size: 1.5rem; color: var(--espresso);">Siapa bawa gerobak</h3>
                    <div class="mini-list">
                        @forelse ($activeAssignments as $assignment)
                            <article class="mini-item">
                                <strong style="display: block; color: var(--espresso);">{{ $assignment->driver?->name }} → {{ $assignment->unit?->name }}</strong>
                                <span class="list-meta" style="display: block; margin-top: 6px;">
                                    Mulai {{ $assignment->assigned_at?->translatedFormat('d M Y H:i') }}
                                </span>
                            </article>
                        @empty
                            <div class="empty-state">Belum ada assignment aktif.</div>
                        @endforelse
                    </div>
                </article>

                <article class="panel-card mini-card">
                    <span class="eyebrow">User Login</span>
                    <h3 style="margin: 10px 0 16px; font-size: 1.5rem; color: var(--espresso);">Daftar user yang sedang aktif</h3>
                    <div class="mini-list">
                        @forelse ($activeUsers as $activeUser)
                            <article class="mini-item">
                                <div style="display: flex; justify-content: space-between; gap: 12px; align-items: start; flex-wrap: wrap;">
                                    <div>
                                        <strong style="display: block; color: var(--espresso);">{{ $activeUser['name'] }}</strong>
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

        <section class="panel-card highlight-panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Ringkasan Utama</span>
                    <h3>Snapshot operasional hari ini</h3>
                </div>
            </div>

            <div class="highlight-grid">
                <article class="profile-card">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($adminEmail ?? 'A', 0, 1)) }}
                    </div>
                    <strong>Admin Dashboard</strong>
                    <span style="color: var(--accent); font-weight: 700;">{{ $adminEmail }}</span>
                    <span style="color: var(--text-soft); max-width: 220px;">
                        Kelola driver, gerobak, sesi login, dan tracking GPS dari satu panel utama.
                    </span>
                </article>

                <div class="info-panel">
                    <article class="info-card">
                        <h4>General information</h4>
                        <div class="info-grid">
                            <div class="info-row">
                                <span>Endpoint Traccar</span>
                                <strong style="word-break: break-all;">{{ $traccarEndpoint }}</strong>
                            </div>
                            <div class="info-row">
                                <span>Assignment aktif</span>
                                <strong>{{ $activeAssignments->count() }} assignment sedang berjalan</strong>
                            </div>
                            <div class="info-row">
                                <span>Driver tersedia</span>
                                <strong>{{ $availableDrivers->count() }} driver siap ditugaskan</strong>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </div>

    <div id="unit-form-modal" class="modal-overlay {{ $errors->hasAny(['name', 'code', 'status', 'notes']) ? 'is-open' : '' }}" aria-hidden="{{ $errors->hasAny(['name', 'code', 'status', 'notes']) ? 'false' : 'true' }}">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">Data Unit</span>
                    <h3>Tambah gerobak</h3>
                    <p class="section-subtext" style="margin: 10px 0 0;">Buat master gerobak baru tanpa mengganggu fitur dashboard.</p>
                </div>
                <button type="button" class="modal-close" data-close-modal="unit-form-modal" aria-label="Tutup">×</button>
            </div>
            <form method="POST" action="{{ route('dashboard.units.store') }}" class="form-stack">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard">
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
        </div>
    </div>

    <div id="driver-form-modal" class="modal-overlay {{ $errors->hasAny(['email', 'device_id', 'password']) ? 'is-open' : '' }}" aria-hidden="{{ $errors->hasAny(['email', 'device_id', 'password']) ? 'false' : 'true' }}">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">Data Driver</span>
                    <h3>Buat akun driver</h3>
                    <p class="section-subtext" style="margin: 10px 0 0;">Akun ini dipakai driver untuk login dan mengirim GPS dari HP sesuai <code>device_id</code> miliknya.</p>
                </div>
                <button type="button" class="modal-close" data-close-modal="driver-form-modal" aria-label="Tutup">×</button>
            </div>
            <form method="POST" action="{{ route('dashboard.drivers.store') }}" class="form-stack">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard">
                <input class="dashboard-input" type="text" name="name" placeholder="Nama driver" value="{{ old('name') }}">
                <input class="dashboard-input" type="email" name="email" placeholder="Email driver" value="{{ old('email') }}">
                <input class="dashboard-input" type="text" name="device_id" placeholder="Device ID HP driver" value="{{ old('device_id') }}">
                <input class="dashboard-input" type="password" name="password" placeholder="Password minimal 8 karakter">
                <button type="submit" class="primary-button">Simpan Driver</button>
            </form>
        </div>
    </div>

    <script>
        const modalOverlays = document.querySelectorAll('.modal-overlay');

        function setModalState(modal, isOpen) {
            if (!modal) {
                return;
            }

            modal.classList.toggle('is-open', isOpen);
            modal.setAttribute('aria-hidden', String(!isOpen));
            document.body.style.overflow = document.querySelector('.modal-overlay.is-open') ? 'hidden' : '';
        }

        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                const modal = document.getElementById(button.dataset.openModal);
                setModalState(modal, true);
                button.setAttribute('aria-expanded', 'true');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                const modal = document.getElementById(button.dataset.closeModal);
                setModalState(modal, false);
            });
        });

        modalOverlays.forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    setModalState(modal, false);
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                modalOverlays.forEach((modal) => setModalState(modal, false));
            }
        });

        if (document.querySelector('.modal-overlay.is-open')) {
            document.body.style.overflow = 'hidden';
        }
    </script>
</x-app-layout>

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

        .action-card {
            display: grid;
            gap: 18px;
            align-content: start;
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

        .modal-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.38);
            backdrop-filter: blur(6px);
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
            border-radius: 22px;
            background: #fff;
            border: 1px solid var(--panel-border);
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.7rem;
        }

        .modal-close {
            width: 40px;
            height: 40px;
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            background: #fff;
            color: var(--text-main);
            cursor: pointer;
            font-size: 1.2rem;
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
            <article class="panel-card section-card action-card">
                <span class="eyebrow">Data Unit</span>
                <h3>Tambah gerobak</h3>
                <p class="section-subtext">Buat master gerobak baru. GPS akan dibaca dari HP driver yang sedang ditugaskan.</p>
                <button
                    type="button"
                    class="primary-button"
                    data-open-modal="unit-form-modal"
                    aria-expanded="{{ $errors->hasAny(['name', 'code', 'status', 'notes']) ? 'true' : 'false' }}"
                >
                    Tambah Gerobak
                </button>
            </article>

            <article class="panel-card section-card action-card">
                <span class="eyebrow">Data Driver</span>
                <h3>Buat akun driver</h3>
                <p class="section-subtext">Akun ini dipakai driver untuk login dan mengirim GPS dari HP sesuai `device_id` miliknya.</p>
                <button
                    type="button"
                    class="primary-button"
                    data-open-modal="driver-form-modal"
                    aria-expanded="{{ $errors->hasAny(['email', 'device_id', 'password']) ? 'true' : 'false' }}"
                >
                    Buat Akun Driver
                </button>
            </article>

            <article class="panel-card section-card action-card">
                <span class="eyebrow">Assignment</span>
                <h3>Assign driver ke gerobak</h3>
                <p class="section-subtext">Satu driver aktif untuk satu gerobak aktif. Assignment lama akan ditutup otomatis.</p>
                <a href="{{ route('dashboard.assignments.index') }}" class="success-button">Buka Halaman Assignment</a>
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

            <div style="display: grid; gap: 20px;">
                <article class="panel-card mini-card">
                    <span class="eyebrow">URL Traccar</span>
                    <h3 style="margin: 0 0 16px; font-size: 1.5rem;">Alamat server aplikasi Traccar</h3>
                    <p class="list-meta" style="margin: 0 0 14px;">
                        Masukkan URL ini ke field <strong>Server URL</strong> di aplikasi Traccar Android driver.
                    </p>
                    <div style="padding: 16px 18px; border-radius: 16px; border: 1px solid var(--panel-border); background: #f8fafc; font-weight: 700; word-break: break-all;">
                        {{ $traccarEndpoint }}
                    </div>
                    <p class="list-meta" style="margin: 14px 0 0;">
                        Isi <strong>Device Identifier</strong> di Traccar dengan <strong>device_id</strong> milik driver.
                    </p>
                </article>

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

    <div id="unit-form-modal" class="modal-overlay {{ $errors->hasAny(['name', 'code', 'status', 'notes']) ? 'is-open' : '' }}" aria-hidden="{{ $errors->hasAny(['name', 'code', 'status', 'notes']) ? 'false' : 'true' }}">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">Data Unit</span>
                    <h3>Tambah gerobak</h3>
                    <p class="section-subtext" style="margin: 10px 0 0;">Buat master gerobak baru tanpa mengganggu layout dashboard.</p>
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
                    <p class="section-subtext" style="margin: 10px 0 0;">Akun ini dipakai driver untuk login dan mengirim GPS dari HP sesuai `device_id` miliknya.</p>
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

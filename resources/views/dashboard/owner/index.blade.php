<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/owner/index.css') }}">
@endpush

    <x-slot name="header">
        <div>
            <div>
                <p class="dashboard-header-kicker">
                    Dashboard Admin
                </p>
                <h2 class="dashboard-header-title">
                    Patient profile style untuk dashboard operasional
                </h2>
                <p class="dashboard-header-subtitle">
                    Tampilan disesuaikan mengikuti referensi klinik: sidebar lembut, kartu putih bersih, dan panel informasi yang lebih rapi tanpa mengubah fitur, isi, atau aksi dashboard.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $unitFormOpen = $errors->unitForm->any();
        $driverFormOpen = $errors->driverForm->any();
    @endphp

    <div class="owner-dashboard">
        <x-dashboard.status-banner :message="session('dashboard_status')" />

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
                <strong class="metric-strong-small">
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
                                    <strong class="unit-name">{{ $unit['name'] }}</strong>
                                    <span class="unit-meta">
                                        {{ $unit['code'] }} &bull; {{ $unit['device_id'] ? 'HP '.$unit['device_id'] : 'Belum ada HP driver aktif' }}
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
                                <form method="POST" action="{{ route('dashboard.assignments.finish', $unit['active_assignment']) }}" class="unit-action-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="redirect_to" value="dashboard">
                                    <button type="submit" class="danger-button">Selesaikan Assignment</button>
                                </form>
                            @endif
                        </article>
                    @empty
                        <x-dashboard.empty-state message="Belum ada gerobak terdaftar." />
                    @endforelse
                </div>
            </article>

            <div class="owner-grid-side">
                <article class="panel-card mini-card">
                    <span class="eyebrow">Assignment Aktif</span>
                    <h3 class="mini-title">Siapa bawa gerobak</h3>
                    <div class="mini-list">
                        @forelse ($activeAssignments as $assignment)
                            <article class="mini-item">
                                <strong class="mini-strong">{{ $assignment->driver?->name }} &rarr; {{ $assignment->unit?->name }}</strong>
                                <span class="list-meta">
                                    Mulai {{ $assignment->assigned_at?->translatedFormat('d M Y H:i') }}
                                </span>
                            </article>
                        @empty
                            <x-dashboard.empty-state message="Belum ada assignment aktif." />
                        @endforelse
                    </div>
                </article>

                <article class="panel-card mini-card">
                    <span class="eyebrow">User Login</span>
                    <h3 class="mini-title">Daftar user yang sedang aktif</h3>
                    <div class="mini-list">
                        @forelse ($activeUsers as $activeUser)
                            <article class="mini-item">
                                <div class="mini-item-row">
                                    <div>
                                        <strong class="mini-user-name">{{ $activeUser['name'] }}</strong>
                                        <span class="list-meta">{{ $activeUser['email'] }}</span>
                                        <span class="list-meta">{{ $activeUser['last_seen']->diffForHumans() }}</span>
                                    </div>
                                    @if (! $activeUser['is_current_admin'])
                                        <form method="POST" action="{{ route('dashboard.users.kick', $activeUser['user_id']) }}" class="inline-form">
                                            @csrf
                                            <button type="submit" class="danger-button">Kick</button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <x-dashboard.empty-state message="Belum ada user aktif." />
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
                    <span class="profile-email">{{ $adminEmail }}</span>
                    <span class="profile-copy">
                        Kelola driver, gerobak, sesi login, dan tracking GPS dari satu panel utama.
                    </span>
                </article>

                <div class="info-panel">
                    <article class="info-card">
                        <h4>General information</h4>
                        <div class="info-grid">
                            <div class="info-row">
                                <span>Endpoint Traccar</span>
                                <strong class="info-endpoint">{{ $traccarEndpoint }}</strong>
                            </div>
                            <div class="info-row">
                                <span>URL untuk QR Absensi Driver</span>
                                <strong class="info-endpoint">
                                    <a href="{{ $driverAttendanceQrLink }}" target="_blank" rel="noopener" class="info-link">
                                        {{ $driverAttendanceQrLink }}
                                    </a>
                                </strong>
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

    <div id="unit-form-modal" class="modal-overlay {{ $unitFormOpen ? 'is-open' : '' }}" aria-hidden="{{ $unitFormOpen ? 'false' : 'true' }}">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">Data Unit</span>
                    <h3>Tambah gerobak</h3>
                    <p class="section-subtext section-subtext-top">Buat master gerobak baru tanpa mengganggu fitur dashboard.</p>
                </div>
                <button type="button" class="modal-close" data-close-modal="unit-form-modal" aria-label="Tutup">&times;</button>
            </div>
            <form method="POST" action="{{ route('dashboard.units.store') }}" class="form-stack">
                @csrf
                <x-dashboard.forms.redirect-fields redirect-to="dashboard" />

                @if ($unitFormOpen)
                    <x-dashboard.validation-list :messages="$errors->unitForm->all()" />
                @endif

                <x-dashboard.forms.unit-fields
                    layout="stack"
                    :name-value="old('name')"
                    :code-value="old('code')"
                    :status-value="old('status', 'ready')"
                    :notes-value="old('notes')"
                />
                <button type="submit" class="primary-button">Simpan Gerobak</button>
            </form>
        </div>
    </div>

    <div id="driver-form-modal" class="modal-overlay {{ $driverFormOpen ? 'is-open' : '' }}" aria-hidden="{{ $driverFormOpen ? 'false' : 'true' }}">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">Data Driver</span>
                    <h3>Buat akun driver</h3>
                    <p class="section-subtext section-subtext-top">Akun ini dipakai driver untuk login dan mengirim GPS dari HP sesuai <code>device_id</code> miliknya.</p>
                </div>
                <button type="button" class="modal-close" data-close-modal="driver-form-modal" aria-label="Tutup">&times;</button>
            </div>
            <form method="POST" action="{{ route('dashboard.drivers.store') }}" class="form-stack">
                @csrf
                <x-dashboard.forms.redirect-fields redirect-to="dashboard" />

                @if ($driverFormOpen)
                    <x-dashboard.validation-list :messages="$errors->driverForm->all()" />
                @endif

                <x-dashboard.forms.driver-fields
                    :name-value="old('name')"
                    :email-value="old('email')"
                    :device-id-value="old('device_id')"
                    :password-required="true"
                />
                <button type="submit" class="primary-button">Simpan Driver</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/pages/dashboard/owner/index.js') }}"></script>
@endpush
</x-app-layout>

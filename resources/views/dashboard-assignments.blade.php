<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard-assignments.css') }}">
@endpush

    <x-slot name="header">
        <div class="page-header-row">
            <div>
                <p class="page-header-kicker">
                    Dashboard Admin
                </p>
                <h2 class="page-header-title">
                    Kelola assignment driver
                </h2>
                <p class="page-header-subtitle">
                    Pilih driver dan gerobak aktif dari halaman khusus assignment.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="section-button">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="assignment-page">
        @if (session('dashboard_status'))
            <section class="status-banner">
                {{ session('dashboard_status') }}
            </section>
        @endif

        <section class="assignment-grid">
            <article class="panel-card section-card">
                <span class="eyebrow">Form Assignment</span>
                <h3 class="section-title">Assign driver ke gerobak</h3>
                <p class="section-subtext section-subtext-gap">
                    Satu driver aktif untuk satu gerobak aktif. Assignment lama akan ditutup otomatis saat dibuat yang baru.
                </p>
                <form method="POST" action="{{ route('dashboard.assignments.store') }}" class="form-stack">
                    @csrf
                    <input type="hidden" name="redirect_to" value="dashboard.assignments.index">
                    <select class="dashboard-select" name="driver_id">
                        <option value="">Pilih driver</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>
                                {{ $driver->name }} - {{ $driver->email }}{{ $driver->activeDriverAssignment ? ' (sedang bertugas)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <select class="dashboard-select" name="unit_id">
                        <option value="">Pilih gerobak</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit['id'] }}" @selected(old('unit_id') == $unit['id'])>
                                {{ $unit['name'] }} ({{ $unit['code'] }}){{ $unit['active_assignment'] ? ' - sedang dipakai' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <textarea class="dashboard-textarea" name="notes" placeholder="Catatan assignment">{{ old('notes') }}</textarea>
                    <button type="submit" class="success-button">Assign Sekarang</button>
                </form>
            </article>

            <article class="panel-card mini-card">
                <span class="eyebrow">Assignment Aktif</span>
                <h3 class="section-title section-title-lg-gap">Daftar penugasan berjalan</h3>
                <div class="mini-list">
                    @forelse ($activeAssignments as $assignment)
                        <article class="mini-item">
                            <div class="assignment-row">
                                <div>
                                    <strong class="assignment-name">{{ $assignment->driver?->name }} &rarr; {{ $assignment->unit?->name }}</strong>
                                    <span class="list-meta">
                                        Device ID: {{ $assignment->driver?->device_id ?? '-' }}
                                    </span>
                                    <span class="list-meta">
                                        Mulai {{ $assignment->assigned_at?->translatedFormat('d M Y H:i') }}
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('dashboard.assignments.finish', $assignment) }}" class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="redirect_to" value="dashboard.assignments.index">
                                    <button type="submit" class="danger-button">Selesaikan</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">Belum ada assignment aktif.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
</x-app-layout>

<x-app-layout>
    <style>
        .assignment-page {
            display: grid;
            gap: 24px;
        }

        .assignment-grid {
            display: grid;
            grid-template-columns: minmax(340px, 0.9fr) minmax(0, 1.1fr);
            gap: 20px;
        }

        .panel-card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }

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

        .section-subtext,
        .list-meta {
            color: var(--text-soft);
            line-height: 1.6;
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
            min-height: 120px;
            resize: vertical;
        }

        .form-stack,
        .mini-list {
            display: grid;
            gap: 12px;
        }

        .section-button,
        .success-button,
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

        .section-button {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--panel-border);
        }

        .success-button {
            background: var(--success);
            color: #fff;
        }

        .danger-button {
            background: rgba(220, 38, 38, 0.12);
            color: var(--danger);
        }

        .mini-item {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid var(--panel-border);
            background: #fff;
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

        @media (max-width: 1080px) {
            .assignment-grid {
                grid-template-columns: 1fr;
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
                    Kelola assignment driver
                </h2>
                <p style="margin: 8px 0 0; color: var(--text-soft);">
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
                <h3 style="margin: 0 0 14px; font-size: 1.7rem;">Assign driver ke gerobak</h3>
                <p class="section-subtext" style="margin: 0 0 18px;">
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
                <h3 style="margin: 0 0 16px; font-size: 1.7rem;">Daftar penugasan berjalan</h3>
                <div class="mini-list">
                    @forelse ($activeAssignments as $assignment)
                        <article class="mini-item">
                            <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                <div>
                                    <strong style="display: block;">{{ $assignment->driver?->name }} &rarr; {{ $assignment->unit?->name }}</strong>
                                    <span class="list-meta" style="display: block; margin-top: 6px;">
                                        Device ID: {{ $assignment->driver?->device_id ?? '-' }}
                                    </span>
                                    <span class="list-meta" style="display: block; margin-top: 6px;">
                                        Mulai {{ $assignment->assigned_at?->translatedFormat('d M Y H:i') }}
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('dashboard.assignments.finish', $assignment) }}" style="margin: 0;">
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

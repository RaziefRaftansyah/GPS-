<x-app-layout>
    <style>
        .manage-page {
            display: grid;
            gap: 22px;
        }

        .manage-grid {
            display: grid;
            grid-template-columns: minmax(340px, 0.95fr) minmax(0, 1.05fr);
            gap: 18px;
            align-items: start;
        }

        .panel-card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 22px;
            box-shadow: var(--shadow-sm);
        }

        .section-card {
            padding: 22px;
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

        .status-banner {
            padding: 15px 17px;
            border-radius: 16px;
            background: rgba(22, 163, 74, 0.12);
            border: 1px solid rgba(22, 163, 74, 0.16);
            color: #166534;
        }

        .validation-list {
            margin: 0;
            padding: 12px 16px 12px 30px;
            border: 1px solid rgba(220, 38, 38, 0.2);
            border-radius: 14px;
            color: var(--danger);
            background: rgba(220, 38, 38, 0.08);
            line-height: 1.5;
        }

        .form-grid {
            display: grid;
            gap: 10px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .dashboard-input,
        .dashboard-select,
        .dashboard-textarea {
            width: 100%;
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            padding: 12px 13px;
            font: inherit;
            color: var(--text-main);
            background: #fff;
        }

        .dashboard-textarea {
            min-height: 84px;
            resize: vertical;
        }

        .checkbox-row {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .section-button,
        .primary-button,
        .danger-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 12px;
            padding: 11px 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .section-button {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--panel-border);
        }

        .primary-button {
            background: var(--success);
            color: #fff;
        }

        .danger-button {
            background: rgba(220, 38, 38, 0.12);
            color: var(--danger);
        }

        .entity-list {
            display: grid;
            gap: 12px;
            margin-top: 12px;
        }

        .entity-item {
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            background: #fff;
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .entity-head {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .status-pill.is-active {
            background: rgba(22, 163, 74, 0.12);
            color: #166534;
        }

        .status-pill.is-inactive {
            background: rgba(100, 116, 139, 0.16);
            color: #334155;
        }

        .button-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .entity-form {
            display: grid;
            gap: 9px;
        }

        .collapsible {
            border: 1px solid var(--panel-border);
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
            scroll-margin-top: 86px;
        }

        .collapsible summary {
            cursor: pointer;
            list-style: none;
            padding: 12px 14px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .collapsible summary::-webkit-details-marker {
            display: none;
        }

        .collapsible summary::after {
            content: '+';
            font-size: 1.05rem;
            line-height: 1;
            color: var(--text-soft);
        }

        .collapsible[open] summary::after {
            content: '-';
        }

        .collapsible-body {
            padding: 0 14px 14px;
            display: grid;
            gap: 10px;
        }

        .list-helper {
            margin: 10px 0 0;
            color: var(--text-soft);
            font-size: 0.9rem;
        }

        .pagination-wrap {
            margin-top: 14px;
        }

        .empty-state {
            padding: 16px;
            border-radius: 14px;
            border: 1px dashed var(--panel-border);
            color: var(--text-soft);
            background: #f8fafc;
        }

        @media (max-width: 1180px) {
            .manage-grid,
            .form-row-2 {
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
                    Kelola Unit & Driver
                </h2>
                <p style="margin: 8px 0 0; color: var(--text-soft);">
                    Satu halaman untuk tambah, edit, dan hapus data unit dan akun driver.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="section-button">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $currentDriverPage = (int) request('driver_page', 1);
        $currentUnitPage = (int) request('unit_page', 1);
        $focus = request('focus');
        $isDriverInputOpen = $focus === 'driver' || $errors->driverForm->any();
        $isUnitInputOpen = $focus === 'unit' || $errors->unitForm->any();
    @endphp

    <div class="manage-page">
        @if (session('dashboard_status'))
            <section class="status-banner">
                {{ session('dashboard_status') }}
            </section>
        @endif

        @if ($errors->any())
            <ul class="validation-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($errors->driverForm->any())
            <ul class="validation-list">
                @foreach ($errors->driverForm->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($errors->unitForm->any())
            <ul class="validation-list">
                @foreach ($errors->unitForm->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <section class="manage-grid">
            <article class="panel-card section-card">
                <span class="eyebrow">Driver</span>
                <h3 style="margin: 0 0 12px; font-size: 1.45rem;">Kelola akun driver</h3>
                <p class="section-subtext" style="margin: 0 0 14px;">
                    Data baru muncul di daftar driver pada halaman ini. Untuk menghindari daftar memanjang, edit dibuat model buka-tutup dan dibagi per halaman.
                </p>

                <details id="driver-input" class="collapsible" @if ($isDriverInputOpen) open @endif>
                    <summary>Tambah driver baru</summary>
                    <div class="collapsible-body">
                        <form method="POST" action="{{ route('dashboard.drivers.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="redirect_to" value="dashboard.manage.index">
                            <input type="hidden" name="driver_page" value="1">
                            <input type="hidden" name="unit_page" value="{{ $currentUnitPage }}">
                            <input class="dashboard-input" type="text" name="name" placeholder="Nama driver" value="{{ $errors->driverForm->any() ? old('name') : '' }}" required>
                            <input class="dashboard-input" type="email" name="email" placeholder="Email driver" value="{{ old('email') }}" required>
                            <input class="dashboard-input" type="text" name="device_id" placeholder="Device ID HP driver" value="{{ old('device_id') }}" required>
                            <input class="dashboard-input" type="password" name="password" placeholder="Password minimal 8 karakter" required>
                            <button type="submit" class="primary-button">Tambah Driver</button>
                        </form>
                    </div>
                </details>

                <p class="list-helper">Menampilkan {{ $drivers->count() }} data per halaman (total {{ $drivers->total() }} driver).</p>

                <div class="entity-list">
                    @forelse ($drivers as $driver)
                        <article class="entity-item">
                            <div class="entity-head">
                                <div>
                                    <strong>{{ $driver->name }}</strong>
                                    <span class="list-meta" style="display: block; margin-top: 4px;">{{ $driver->email }}</span>
                                </div>
                                <span class="status-pill {{ $driver->is_active ? 'is-active' : 'is-inactive' }}">
                                    {{ $driver->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            @if ($driver->activeDriverAssignment)
                                <span class="list-meta">
                                    Assignment aktif: {{ $driver->activeDriverAssignment->unit?->name ?? 'Unit tidak ditemukan' }}
                                </span>
                            @endif

                            <details class="collapsible">
                                <summary>Edit data driver</summary>
                                <div class="collapsible-body">
                                    <form method="POST" action="{{ route('dashboard.drivers.update', $driver) }}" class="entity-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="redirect_to" value="dashboard.manage.index">
                                        <input type="hidden" name="driver_page" value="{{ $currentDriverPage }}">
                                        <input type="hidden" name="unit_page" value="{{ $currentUnitPage }}">

                                        <div class="form-row-2">
                                            <input class="dashboard-input" type="text" name="name" value="{{ $driver->name }}" required>
                                            <input class="dashboard-input" type="email" name="email" value="{{ $driver->email }}" required>
                                        </div>

                                        <div class="form-row-2">
                                            <input class="dashboard-input" type="text" name="device_id" value="{{ $driver->device_id }}" required>
                                            <input class="dashboard-input" type="password" name="password" placeholder="Kosongkan jika tidak ganti password">
                                        </div>

                                        <label class="checkbox-row">
                                            <input type="checkbox" name="is_active" value="1" @checked($driver->is_active)>
                                            <span>Akun aktif</span>
                                        </label>

                                        <div class="button-row">
                                            <button type="submit" class="primary-button">Simpan Driver</button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('dashboard.drivers.destroy', $driver) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="dashboard.manage.index">
                                        <input type="hidden" name="driver_page" value="{{ $currentDriverPage }}">
                                        <input type="hidden" name="unit_page" value="{{ $currentUnitPage }}">
                                        <button type="submit" class="danger-button">Hapus Driver</button>
                                    </form>
                                </div>
                            </details>
                        </article>
                    @empty
                        <div class="empty-state">Belum ada data driver.</div>
                    @endforelse
                </div>

                <div class="pagination-wrap">
                    {{ $drivers->links() }}
                </div>
            </article>

            <article class="panel-card section-card">
                <span class="eyebrow">Unit</span>
                <h3 style="margin: 0 0 12px; font-size: 1.45rem;">Kelola data gerobak</h3>
                <p class="section-subtext" style="margin: 0 0 14px;">
                    Data unit tampil di daftar bawah. Supaya tetap rapi saat data banyak, daftar dibatasi per halaman dan form edit dibuat lipat.
                </p>

                <details id="unit-input" class="collapsible" @if ($isUnitInputOpen) open @endif>
                    <summary>Tambah unit baru</summary>
                    <div class="collapsible-body">
                        <form method="POST" action="{{ route('dashboard.units.store') }}" class="form-grid">
                            @csrf
                            <input type="hidden" name="redirect_to" value="dashboard.manage.index">
                            <input type="hidden" name="driver_page" value="{{ $currentDriverPage }}">
                            <input type="hidden" name="unit_page" value="1">
                            <div class="form-row-2">
                                <input class="dashboard-input" type="text" name="name" placeholder="Nama gerobak" value="{{ $errors->unitForm->any() ? old('name') : '' }}" required>
                                <input class="dashboard-input" type="text" name="code" placeholder="Kode unit, contoh GRBK-01" value="{{ old('code') }}" required>
                            </div>
                            <select class="dashboard-select" name="status" required>
                                <option value="ready" @selected(old('status', 'ready') === 'ready')>Siap Operasi</option>
                                <option value="maintenance" @selected(old('status') === 'maintenance')>Maintenance</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Nonaktif</option>
                            </select>
                            <textarea class="dashboard-textarea" name="notes" placeholder="Catatan unit">{{ old('notes') }}</textarea>
                            <button type="submit" class="primary-button">Tambah Unit</button>
                        </form>
                    </div>
                </details>

                <p class="list-helper">Menampilkan {{ $units->count() }} data per halaman (total {{ $units->total() }} unit).</p>

                <div class="entity-list">
                    @forelse ($units as $unit)
                        @php
                            $activeAssignment = $unit->assignments->first(
                                fn (\App\Models\DriverUnitAssignment $assignment): bool => $assignment->status === 'active' && $assignment->ended_at === null
                            );
                        @endphp
                        <article class="entity-item">
                            <div class="entity-head">
                                <div>
                                    <strong>{{ $unit->name }}</strong>
                                    <span class="list-meta" style="display: block; margin-top: 4px;">{{ $unit->code }}</span>
                                </div>
                                <span class="status-pill {{ $unit->status === 'ready' ? 'is-active' : 'is-inactive' }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </div>

                            @if ($activeAssignment)
                                <span class="list-meta">
                                    Dipakai oleh: {{ $activeAssignment->driver?->name ?? 'Driver tidak ditemukan' }}
                                </span>
                            @endif

                            <details class="collapsible">
                                <summary>Edit data unit</summary>
                                <div class="collapsible-body">
                                    <form method="POST" action="{{ route('dashboard.units.update', $unit) }}" class="entity-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="redirect_to" value="dashboard.manage.index">
                                        <input type="hidden" name="driver_page" value="{{ $currentDriverPage }}">
                                        <input type="hidden" name="unit_page" value="{{ $currentUnitPage }}">

                                        <div class="form-row-2">
                                            <input class="dashboard-input" type="text" name="name" value="{{ $unit->name }}" required>
                                            <input class="dashboard-input" type="text" name="code" value="{{ $unit->code }}" required>
                                        </div>

                                        <select class="dashboard-select" name="status" required>
                                            <option value="ready" @selected($unit->status === 'ready')>Siap Operasi</option>
                                            <option value="maintenance" @selected($unit->status === 'maintenance')>Maintenance</option>
                                            <option value="inactive" @selected($unit->status === 'inactive')>Nonaktif</option>
                                        </select>

                                        <textarea class="dashboard-textarea" name="notes" placeholder="Catatan unit">{{ $unit->notes }}</textarea>

                                        <div class="button-row">
                                            <button type="submit" class="primary-button">Simpan Unit</button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('dashboard.units.destroy', $unit) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="dashboard.manage.index">
                                        <input type="hidden" name="driver_page" value="{{ $currentDriverPage }}">
                                        <input type="hidden" name="unit_page" value="{{ $currentUnitPage }}">
                                        <button type="submit" class="danger-button">Hapus Unit</button>
                                    </form>
                                </div>
                            </details>
                        </article>
                    @empty
                        <div class="empty-state">Belum ada data unit.</div>
                    @endforelse
                </div>

                <div class="pagination-wrap">
                    {{ $units->links() }}
                </div>
            </article>
        </section>
    </div>
</x-app-layout>

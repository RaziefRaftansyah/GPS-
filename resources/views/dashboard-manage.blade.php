<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard-manage.css') }}">
@endpush

    <x-slot name="header">
        <div class="page-header-row">
            <div>
                <p class="page-header-kicker">
                    Dashboard Admin
                </p>
                <h2 class="page-header-title">
                    Kelola Unit & Driver
                </h2>
                <p class="page-header-subtitle">
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
                <h3 class="section-title">Kelola akun driver</h3>
                <p class="section-subtext section-subtext-gap">
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
                                    <span class="list-meta list-meta-compact">{{ $driver->email }}</span>
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
                <h3 class="section-title">Kelola data gerobak</h3>
                <p class="section-subtext section-subtext-gap">
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
                                    <span class="list-meta list-meta-compact">{{ $unit->code }}</span>
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

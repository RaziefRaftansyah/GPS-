<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/owner/resources.css') }}">
@endpush

    <x-slot name="header">
        <x-dashboard.owner-page-header
            title="Kelola Unit & Driver"
            subtitle="Satu halaman untuk tambah, edit, dan hapus data unit dan akun driver."
        />
    </x-slot>

    @php
        $currentDriverPage = (int) request('driver_page', 1);
        $currentUnitPage = (int) request('unit_page', 1);
        $focus = request('focus');
        $isDriverInputOpen = $focus === 'driver' || $errors->driverForm->any();
        $isUnitInputOpen = $focus === 'unit' || $errors->unitForm->any();
    @endphp

    <div class="manage-page">
        <x-dashboard.status-banner :message="session('dashboard_status')" />
        <x-dashboard.validation-list :messages="$errors->all()" />
        <x-dashboard.validation-list :messages="$errors->driverForm->all()" />
        <x-dashboard.validation-list :messages="$errors->unitForm->all()" />

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
                            <x-dashboard.forms.redirect-fields
                                redirect-to="dashboard.manage.index"
                                :driver-page="1"
                                :unit-page="$currentUnitPage"
                            />
                            <x-dashboard.forms.driver-fields
                                :name-value="$errors->driverForm->any() ? old('name') : ''"
                                :email-value="old('email')"
                                :device-id-value="old('device_id')"
                                :password-required="true"
                            />
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
                                        <x-dashboard.forms.redirect-fields
                                            redirect-to="dashboard.manage.index"
                                            :driver-page="$currentDriverPage"
                                            :unit-page="$currentUnitPage"
                                        />
                                        <x-dashboard.forms.driver-fields
                                            layout="split"
                                            :name-value="$driver->name"
                                            :email-value="$driver->email"
                                            :device-id-value="$driver->device_id"
                                            password-placeholder="Kosongkan jika tidak ganti password"
                                            :show-active="true"
                                            :is-active="(bool) $driver->is_active"
                                        />

                                        <div class="button-row">
                                            <button type="submit" class="primary-button">Simpan Driver</button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('dashboard.drivers.destroy', $driver) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-dashboard.forms.redirect-fields
                                            redirect-to="dashboard.manage.index"
                                            :driver-page="$currentDriverPage"
                                            :unit-page="$currentUnitPage"
                                        />
                                        <button type="submit" class="danger-button">Hapus Driver</button>
                                    </form>
                                </div>
                            </details>
                        </article>
                    @empty
                        <x-dashboard.empty-state message="Belum ada data driver." />
                    @endforelse
                </div>

                <div class="pagination-wrap">
                    {{ $drivers->onEachSide(1)->links('vendor.pagination.dashboard-manage') }}
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
                            <x-dashboard.forms.redirect-fields
                                redirect-to="dashboard.manage.index"
                                :driver-page="$currentDriverPage"
                                :unit-page="1"
                            />
                            <x-dashboard.forms.unit-fields
                                :name-value="$errors->unitForm->any() ? old('name') : ''"
                                :code-value="old('code')"
                                :status-value="old('status', 'ready')"
                                :notes-value="old('notes')"
                            />
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
                                        <x-dashboard.forms.redirect-fields
                                            redirect-to="dashboard.manage.index"
                                            :driver-page="$currentDriverPage"
                                            :unit-page="$currentUnitPage"
                                        />
                                        <x-dashboard.forms.unit-fields
                                            :name-value="$unit->name"
                                            :code-value="$unit->code"
                                            :status-value="$unit->status"
                                            :notes-value="$unit->notes"
                                        />

                                        <div class="button-row">
                                            <button type="submit" class="primary-button">Simpan Unit</button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('dashboard.units.destroy', $unit) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-dashboard.forms.redirect-fields
                                            redirect-to="dashboard.manage.index"
                                            :driver-page="$currentDriverPage"
                                            :unit-page="$currentUnitPage"
                                        />
                                        <button type="submit" class="danger-button">Hapus Unit</button>
                                    </form>
                                </div>
                            </details>
                        </article>
                    @empty
                        <x-dashboard.empty-state message="Belum ada data unit." />
                    @endforelse
                </div>

                <div class="pagination-wrap">
                    {{ $units->onEachSide(1)->links('vendor.pagination.dashboard-manage') }}
                </div>
            </article>
        </section>
    </div>
</x-app-layout>

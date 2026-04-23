<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/driver/products.css') }}">
    @endpush

    <x-slot name="header">
        <div class="driver-products-header">
            <div>
                <p class="driver-products-kicker">Produk Driver</p>
                <h2 class="driver-products-title">Pilih produk kopi yang ingin kamu jual</h2>
                <p class="driver-products-subtitle">
                    Pilihan ini dipakai untuk menentukan katalog menu yang tampil saat pelanggan klik pin point driver kamu di peta publik.
                </p>
            </div>
            <span class="driver-products-badge">{{ count($selectedMenuIds) }} terpilih</span>
        </div>
    </x-slot>

    <div class="driver-products-page">
        @if (session('dashboard_status'))
            <section class="driver-products-alert">
                {{ session('dashboard_status') }}
            </section>
        @endif

        <section class="driver-products-panel">
            <div class="driver-products-panel-head">
                <div>
                    <span class="driver-products-panel-kicker">Daftar Menu Aktif</span>
                    <h3>Checklist menu jualan hari ini</h3>
                    <p>Centang menu yang mau dijual dari gerobak kamu, lalu simpan.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.driver.products.update') }}" class="driver-products-form">
                @csrf
                @method('PUT')

                <div class="driver-products-grid">
                    @forelse ($menus as $menu)
                        <label class="driver-product-item">
                            <input
                                type="checkbox"
                                name="menu_ids[]"
                                value="{{ $menu->id }}"
                                @checked(in_array((int) $menu->id, $selectedMenuIds, true))
                            >

                            <div class="driver-product-item-copy">
                                <strong>{{ $menu->name }}</strong>
                                <span>{{ $menu->category }} | Rp{{ number_format((int) $menu->price, 0, ',', '.') }}</span>
                                <p>{{ $menu->description ?: 'Menu tersedia dari katalog owner.' }}</p>
                            </div>
                        </label>
                    @empty
                        <div class="driver-products-empty">
                            Belum ada menu aktif dari owner. Hubungi owner untuk menambahkan katalog terlebih dahulu.
                        </div>
                    @endforelse
                </div>

                @error('menu_ids')
                    <p class="driver-products-error">{{ $message }}</p>
                @enderror
                @error('menu_ids.*')
                    <p class="driver-products-error">{{ $message }}</p>
                @enderror

                <div class="driver-products-actions">
                    <button type="submit" class="driver-products-primary">Simpan Pilihan Produk</button>
                    <a href="{{ route('dashboard') }}" class="driver-products-ghost">Kembali ke Dashboard Driver</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>

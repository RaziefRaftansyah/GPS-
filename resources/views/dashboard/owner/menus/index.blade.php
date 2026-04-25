<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/owner/menus.css') }}">
@endpush

    <x-slot name="header">
        <x-dashboard.owner-page-header
            title="Kelola katalog menu"
            subtitle="Tambah, ubah, aktifkan/nonaktifkan, dan hapus menu yang ditampilkan di halaman tracker publik."
        />
    </x-slot>

    <div class="menu-dashboard">
        <x-dashboard.status-banner :message="session('dashboard_status')" />
        <x-dashboard.validation-list :messages="$errors->all()" />

        <section class="menu-grid">
            <article class="panel-card section-card">
                <span class="eyebrow">Tambah Menu</span>
                <h3 class="section-title">Menu baru</h3>
                <p class="section-subtext section-subtext-gap">
                    Semua data disimpan ke database. Menu aktif akan otomatis muncul di katalog halaman utama.
                </p>

                <form method="POST" action="{{ route('dashboard.menus.store') }}" class="form-grid" enctype="multipart/form-data">
                    @csrf
                    <x-dashboard.forms.redirect-fields redirect-to="dashboard.menus.index" />
                    <x-dashboard.forms.menu-fields
                        image-file-input-id="create-image-file"
                        :name-value="old('name')"
                        :category-value="old('category', 'Coffee')"
                        :price-value="old('price')"
                        :sort-order-value="old('sort_order', 0)"
                        :tags-value="old('tags_input')"
                        :image-path-value="old('image_path')"
                        :description-value="old('description')"
                        :is-active="old('is_active', true)"
                    />

                    <button type="submit" class="primary-button">Simpan Menu</button>
                </form>
            </article>

            <article class="panel-card mini-card">
                <span class="eyebrow">Daftar Menu</span>
                <h3 class="section-title section-title-lg-gap">Edit atau hapus menu</h3>

                <div class="menu-list">
                    @forelse ($menus as $menu)
                        <article class="menu-item">
                            <div class="menu-item-header">
                                <div class="menu-item-main">
                                    @if ($menu->image_path)
                                        <img class="menu-summary-thumb" src="{{ \Illuminate\Support\Str::startsWith($menu->image_path, ['http://', 'https://']) ? $menu->image_path : asset(ltrim($menu->image_path, '/')) }}" alt="{{ $menu->name }}">
                                    @endif
                                    <div class="menu-item-meta">
                                        <strong>{{ $menu->name }}</strong>
                                        <span class="list-meta">
                                            {{ $menu->category }} | Rp{{ number_format($menu->price, 0, ',', '.') }} | Urutan: {{ $menu->sort_order }}
                                        </span>
                                    </div>
                                </div>
                                <div class="menu-item-actions">
                                    <span class="menu-status {{ $menu->is_active ? 'is-active' : 'is-inactive' }}">
                                        {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>

                                    <button
                                        type="button"
                                        class="section-button menu-edit-toggle"
                                        data-edit-toggle
                                        data-target-id="menu-edit-{{ $menu->id }}"
                                        data-label-open="Tutup Edit"
                                        data-label-close="Edit"
                                        aria-expanded="false"
                                    >
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('dashboard.menus.destroy', $menu) }}" class="menu-inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="dashboard.menus.index">
                                        <button type="submit" class="danger-button">Hapus</button>
                                    </form>
                                </div>
                            </div>

                            <div id="menu-edit-{{ $menu->id }}" class="menu-edit-panel" hidden>
                                <form method="POST" action="{{ route('dashboard.menus.update', $menu) }}" class="form-grid" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <x-dashboard.forms.redirect-fields redirect-to="dashboard.menus.index" />
                                    <x-dashboard.forms.menu-fields
                                        :image-file-input-id="'edit-image-file-'.$menu->id"
                                        image-label="Ganti gambar (file manager)"
                                        image-help="Upload file baru jika ingin mengganti gambar menu."
                                        image-path-placeholder="Opsional: URL/path manual"
                                        :name-value="$menu->name"
                                        :category-value="$menu->category"
                                        :price-value="$menu->price"
                                        :sort-order-value="$menu->sort_order"
                                        :tags-value="implode(',', $menu->tags ?? [])"
                                        :image-path-value="$menu->image_path"
                                        :description-value="$menu->description"
                                        :is-active="(bool) $menu->is_active"
                                        :show-current-image="filled($menu->image_path)"
                                        :show-remove-image="filled($menu->image_path)"
                                        :menu-name="$menu->name"
                                        active-label="Tampilkan di tracker publik"
                                    />

                                    <div class="button-row">
                                        <button type="submit" class="primary-button">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    @empty
                        <x-dashboard.empty-state message="Belum ada data menu. Tambahkan menu pertama dari form di sebelah kiri." />
                    @endforelse
                </div>
            </article>
        </section>
    </div>

    @push('scripts')
    <script src="{{ asset('js/pages/dashboard/owner/menus.js') }}"></script>
@endpush
</x-app-layout>

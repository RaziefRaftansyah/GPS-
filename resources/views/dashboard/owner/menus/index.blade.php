<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard/owner/menus.css') }}">
@endpush

    <x-slot name="header">
        <div class="page-header-row">
            <div>
                <p class="page-header-kicker">
                    Dashboard Admin
                </p>
                <h2 class="page-header-title">
                    Kelola katalog menu
                </h2>
                <p class="page-header-subtitle">
                    Tambah, ubah, aktifkan/nonaktifkan, dan hapus menu yang ditampilkan di halaman tracker publik.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="section-button">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="menu-dashboard">
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

        <section class="menu-grid">
            <article class="panel-card section-card">
                <span class="eyebrow">Tambah Menu</span>
                <h3 class="section-title">Menu baru</h3>
                <p class="section-subtext section-subtext-gap">
                    Semua data disimpan ke database. Menu aktif akan otomatis muncul di katalog halaman utama.
                </p>

                <form method="POST" action="{{ route('dashboard.menus.store') }}" class="form-grid" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="redirect_to" value="dashboard.menus.index">

                    <input class="dashboard-input" type="text" name="name" placeholder="Nama menu" value="{{ old('name') }}" required>

                    <div class="form-row-2">
                        <input class="dashboard-input" type="text" name="category" placeholder="Kategori, contoh Coffee" value="{{ old('category', 'Coffee') }}" required>
                        <input class="dashboard-input" type="number" name="price" min="0" step="1" placeholder="Harga rupiah, contoh 12000" value="{{ old('price') }}" required>
                    </div>

                    <div class="form-row-2">
                        <input class="dashboard-input" type="number" name="sort_order" min="0" step="1" placeholder="Urutan tampil" value="{{ old('sort_order', 0) }}">
                        <input class="dashboard-input" type="text" name="tags_input" placeholder="Tag dipisah koma, contoh creamy,sweet" value="{{ old('tags_input') }}">
                    </div>

                    <label class="field-label" for="create-image-file">Gambar menu (dari file manager)</label>
                    <input id="create-image-file" class="dashboard-input" type="file" name="image_file" accept="image/*">
                    <p class="field-help">Pilih file gambar dari komputer. Kosongkan jika menu tanpa foto.</p>

                    <input class="dashboard-input" type="text" name="image_path" placeholder="Opsional: URL/path manual jika perlu override" value="{{ old('image_path') }}">
                    <textarea class="dashboard-textarea" name="description" placeholder="Deskripsi menu">{{ old('description') }}</textarea>

                    <label class="checkbox-row">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                        <span>Aktif (ditampilkan di tracker publik)</span>
                    </label>

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
                                    <input type="hidden" name="redirect_to" value="dashboard.menus.index">

                                    <input class="dashboard-input" type="text" name="name" value="{{ $menu->name }}" required>

                                    <div class="form-row-2">
                                        <input class="dashboard-input" type="text" name="category" value="{{ $menu->category }}" required>
                                        <input class="dashboard-input" type="number" name="price" min="0" step="1" value="{{ $menu->price }}" required>
                                    </div>

                                    <div class="form-row-2">
                                        <input class="dashboard-input" type="number" name="sort_order" min="0" step="1" value="{{ $menu->sort_order }}">
                                        <input class="dashboard-input" type="text" name="tags_input" value="{{ implode(',', $menu->tags ?? []) }}">
                                    </div>

                                    @if ($menu->image_path)
                                        <div class="menu-image-current">
                                            <img class="menu-thumb" src="{{ \Illuminate\Support\Str::startsWith($menu->image_path, ['http://', 'https://']) ? $menu->image_path : asset(ltrim($menu->image_path, '/')) }}" alt="{{ $menu->name }}">
                                            <span class="field-help field-help-inline">Gambar saat ini: {{ $menu->image_path }}</span>
                                        </div>
                                    @endif

                                    <label class="field-label">Ganti gambar (file manager)</label>
                                    <input class="dashboard-input" type="file" name="image_file" accept="image/*">
                                    <p class="field-help">Upload file baru jika ingin mengganti gambar menu.</p>

                                    <input class="dashboard-input" type="text" name="image_path" value="{{ $menu->image_path }}" placeholder="Opsional: URL/path manual">

                                    @if ($menu->image_path)
                                        <label class="checkbox-row">
                                            <input type="checkbox" name="remove_image" value="1">
                                            <span>Hapus gambar saat ini</span>
                                        </label>
                                    @endif
                                    <textarea class="dashboard-textarea" name="description">{{ $menu->description }}</textarea>

                                    <label class="checkbox-row">
                                        <input type="checkbox" name="is_active" value="1" @checked($menu->is_active)>
                                        <span>Tampilkan di tracker publik</span>
                                    </label>

                                    <div class="button-row">
                                        <button type="submit" class="primary-button">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">Belum ada data menu. Tambahkan menu pertama dari form di sebelah kiri.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>

    @push('scripts')
    <script src="{{ asset('js/pages/dashboard/owner/menus.js') }}"></script>
@endpush
</x-app-layout>

<x-app-layout>
    <style>
        .menu-dashboard {
            display: grid;
            gap: 24px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: minmax(340px, 0.82fr) minmax(0, 1.18fr);
            gap: 20px;
            align-items: start;
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

        .status-banner {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(22, 163, 74, 0.12);
            border: 1px solid rgba(22, 163, 74, 0.16);
            color: #166534;
        }

        .validation-list {
            margin: 0;
            padding: 14px 16px 14px 30px;
            border: 1px solid rgba(220, 38, 38, 0.2);
            border-radius: 14px;
            color: var(--danger);
            background: rgba(220, 38, 38, 0.08);
            line-height: 1.55;
        }

        .form-grid {
            display: grid;
            gap: 12px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .dashboard-input,
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
            min-height: 100px;
            resize: vertical;
        }

        .field-label {
            font-size: 0.86rem;
            color: var(--text-soft);
            font-weight: 700;
        }

        .field-help {
            margin: -6px 0 0;
            color: var(--text-soft);
            font-size: 0.82rem;
            line-height: 1.5;
        }

        .menu-thumb {
            width: 78px;
            height: 78px;
            border-radius: 12px;
            border: 1px solid var(--panel-border);
            object-fit: cover;
            background: #fff;
        }

        .menu-list {
            display: grid;
            gap: 12px;
        }

        .menu-item {
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            background: #fff;
            padding: 16px;
            display: grid;
            gap: 12px;
        }

        .menu-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .menu-item-main {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1;
        }

        .menu-item-meta {
            min-width: 0;
        }

        .menu-item-meta strong {
            display: block;
            font-size: 1.1rem;
            line-height: 1.2;
            word-break: break-word;
        }

        .menu-summary-thumb {
            width: 54px;
            height: 54px;
            border-radius: 10px;
            border: 1px solid var(--panel-border);
            object-fit: cover;
            background: #fff;
            flex-shrink: 0;
        }

        .menu-item-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .menu-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .menu-status.is-active {
            background: rgba(22, 163, 74, 0.12);
            color: #166534;
        }

        .menu-status.is-inactive {
            background: rgba(100, 116, 139, 0.16);
            color: #334155;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .button-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .menu-inline-form {
            margin: 0;
        }

        .menu-edit-toggle {
            padding: 10px 14px;
        }

        .menu-edit-panel {
            border-top: 1px dashed var(--panel-border);
            padding-top: 12px;
            display: grid;
            gap: 12px;
        }

        .menu-edit-panel[hidden] {
            display: none !important;
        }

        .section-button,
        .primary-button,
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

        .primary-button {
            background: var(--success);
            color: #fff;
        }

        .danger-button {
            background: rgba(220, 38, 38, 0.12);
            color: var(--danger);
        }

        .empty-state {
            padding: 16px;
            border-radius: 14px;
            border: 1px dashed var(--panel-border);
            color: var(--text-soft);
            background: #f8fafc;
        }

        @media (max-width: 1080px) {
            .menu-grid,
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
                    Kelola katalog menu
                </h2>
                <p style="margin: 8px 0 0; color: var(--text-soft);">
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
                <h3 style="margin: 0 0 14px; font-size: 1.65rem;">Menu baru</h3>
                <p class="section-subtext" style="margin: 0 0 18px;">
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
                <h3 style="margin: 0 0 16px; font-size: 1.65rem;">Edit atau hapus menu</h3>

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
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <img class="menu-thumb" src="{{ \Illuminate\Support\Str::startsWith($menu->image_path, ['http://', 'https://']) ? $menu->image_path : asset(ltrim($menu->image_path, '/')) }}" alt="{{ $menu->name }}">
                                            <span class="field-help" style="margin: 0;">Gambar saat ini: {{ $menu->image_path }}</span>
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

    <script>
        const menuEditToggles = document.querySelectorAll('[data-edit-toggle]');

        function setMenuEditPanelState(button, isOpen) {
            const panel = document.getElementById(button.dataset.targetId);

            if (!panel) {
                return;
            }

            panel.hidden = !isOpen;
            button.setAttribute('aria-expanded', String(isOpen));
            button.textContent = isOpen ? button.dataset.labelOpen : button.dataset.labelClose;
        }

        menuEditToggles.forEach((button) => {
            setMenuEditPanelState(button, false);

            button.addEventListener('click', () => {
                const panel = document.getElementById(button.dataset.targetId);

                if (!panel) {
                    return;
                }

                const shouldOpen = panel.hidden;

                menuEditToggles.forEach((otherButton) => {
                    if (otherButton !== button) {
                        setMenuEditPanelState(otherButton, false);
                    }
                });

                setMenuEditPanelState(button, shouldOpen);
            });
        });
    </script>
</x-app-layout>

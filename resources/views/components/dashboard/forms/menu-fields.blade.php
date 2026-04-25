@props([
    'nameValue' => '',
    'categoryValue' => 'Coffee',
    'priceValue' => null,
    'sortOrderValue' => 0,
    'tagsValue' => '',
    'imagePathValue' => '',
    'descriptionValue' => '',
    'isActive' => true,
    'imageFileInputId' => 'menu-image-file',
    'imageLabel' => 'Gambar menu (dari file manager)',
    'imageHelp' => 'Pilih file gambar dari komputer. Kosongkan jika menu tanpa foto.',
    'imagePathPlaceholder' => 'Opsional: URL/path manual jika perlu override',
    'showCurrentImage' => false,
    'showRemoveImage' => false,
    'menuName' => 'Menu',
    'activeLabel' => 'Aktif (ditampilkan di tracker publik)',
])

<input class="dashboard-input" type="text" name="name" placeholder="Nama menu" value="{{ $nameValue }}" required>

<div class="form-row-2">
    <input class="dashboard-input" type="text" name="category" placeholder="Kategori, contoh Coffee" value="{{ $categoryValue }}" required>
    <input class="dashboard-input" type="number" name="price" min="0" step="1" placeholder="Harga rupiah, contoh 12000" value="{{ $priceValue }}" required>
</div>

<div class="form-row-2">
    <input class="dashboard-input" type="number" name="sort_order" min="0" step="1" placeholder="Urutan tampil" value="{{ $sortOrderValue }}">
    <input class="dashboard-input" type="text" name="tags_input" placeholder="Tag dipisah koma, contoh creamy,sweet" value="{{ $tagsValue }}">
</div>

@if ($showCurrentImage && filled($imagePathValue))
    <div class="menu-image-current">
        <img
            class="menu-thumb"
            src="{{ \Illuminate\Support\Str::startsWith($imagePathValue, ['http://', 'https://']) ? $imagePathValue : asset(ltrim($imagePathValue, '/')) }}"
            alt="{{ $menuName }}"
        >
        <span class="field-help field-help-inline">Gambar saat ini: {{ $imagePathValue }}</span>
    </div>
@endif

<label class="field-label" for="{{ $imageFileInputId }}">{{ $imageLabel }}</label>
<input id="{{ $imageFileInputId }}" class="dashboard-input" type="file" name="image_file" accept="image/*">
<p class="field-help">{{ $imageHelp }}</p>

<input class="dashboard-input" type="text" name="image_path" placeholder="{{ $imagePathPlaceholder }}" value="{{ $imagePathValue }}">

@if ($showRemoveImage)
    <label class="checkbox-row">
        <input type="checkbox" name="remove_image" value="1">
        <span>Hapus gambar saat ini</span>
    </label>
@endif

<textarea class="dashboard-textarea" name="description" placeholder="Deskripsi menu">{{ $descriptionValue }}</textarea>

<label class="checkbox-row">
    <input type="checkbox" name="is_active" value="1" @checked($isActive)>
    <span>{{ $activeLabel }}</span>
</label>

@props([
    'nameValue' => '',
    'codeValue' => '',
    'statusValue' => 'ready',
    'notesValue' => '',
    'layout' => 'split',
])

@if ($layout === 'split')
    <div class="form-row-2">
        <input class="dashboard-input" type="text" name="name" value="{{ $nameValue }}" placeholder="Nama gerobak" required>
        <input class="dashboard-input" type="text" name="code" value="{{ $codeValue }}" placeholder="Kode unit, contoh GRBK-01" required>
    </div>
@else
    <input class="dashboard-input" type="text" name="name" value="{{ $nameValue }}" placeholder="Nama gerobak" required>
    <input class="dashboard-input" type="text" name="code" value="{{ $codeValue }}" placeholder="Kode unit, contoh GRBK-01" required>
@endif

<select class="dashboard-select" name="status" required>
    <option value="ready" @selected($statusValue === 'ready')>Siap Operasi</option>
    <option value="maintenance" @selected($statusValue === 'maintenance')>Maintenance</option>
    <option value="inactive" @selected($statusValue === 'inactive')>Nonaktif</option>
</select>

<textarea class="dashboard-textarea" name="notes" placeholder="Catatan unit">{{ $notesValue }}</textarea>

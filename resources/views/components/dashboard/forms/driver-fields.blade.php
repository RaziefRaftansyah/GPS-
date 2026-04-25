@props([
    'nameValue' => '',
    'emailValue' => '',
    'deviceIdValue' => '',
    'passwordPlaceholder' => 'Password minimal 8 karakter',
    'passwordRequired' => false,
    'showActive' => false,
    'isActive' => true,
    'layout' => 'stack',
])

@if ($layout === 'split')
    <div class="form-row-2">
        <input class="dashboard-input" type="text" name="name" value="{{ $nameValue }}" placeholder="Nama driver" required>
        <input class="dashboard-input" type="email" name="email" value="{{ $emailValue }}" placeholder="Email driver" required>
    </div>

    <div class="form-row-2">
        <input class="dashboard-input" type="text" name="device_id" value="{{ $deviceIdValue }}" placeholder="Device ID HP driver" required>
        <input
            class="dashboard-input"
            type="password"
            name="password"
            placeholder="{{ $passwordPlaceholder }}"
            @if ($passwordRequired) required @endif
        >
    </div>
@else
    <input class="dashboard-input" type="text" name="name" placeholder="Nama driver" value="{{ $nameValue }}" required>
    <input class="dashboard-input" type="email" name="email" placeholder="Email driver" value="{{ $emailValue }}" required>
    <input class="dashboard-input" type="text" name="device_id" placeholder="Device ID HP driver" value="{{ $deviceIdValue }}" required>
    <input
        class="dashboard-input"
        type="password"
        name="password"
        placeholder="{{ $passwordPlaceholder }}"
        @if ($passwordRequired) required @endif
    >
@endif

@if ($showActive)
    <label class="checkbox-row">
        <input type="checkbox" name="is_active" value="1" @checked($isActive)>
        <span>Akun aktif</span>
    </label>
@endif

@props([
    'title',
    'subtitle' => null,
    'kicker' => 'Dashboard Admin',
    'backRoute' => 'dashboard',
    'backLabel' => 'Kembali ke Dashboard',
])

<div class="page-header-row">
    <div>
        <p class="page-header-kicker">
            {{ $kicker }}
        </p>
        <h2 class="page-header-title">
            {{ $title }}
        </h2>
        @if (filled($subtitle))
            <p class="page-header-subtitle">
                {{ $subtitle }}
            </p>
        @endif
    </div>
    <a href="{{ route($backRoute) }}" class="section-button">
        {{ $backLabel }}
    </a>
</div>

@props([
    'message' => null,
])

@php
    $classes = trim((string) $attributes->get('class'));
@endphp

@if (filled($message))
    <section {{ $attributes->except('class')->merge(['class' => $classes !== '' ? $classes : 'status-banner']) }}>
        {{ $message }}
    </section>
@endif

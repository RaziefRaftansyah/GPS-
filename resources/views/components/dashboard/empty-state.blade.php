@props([
    'message',
])

@php
    $classes = trim((string) $attributes->get('class'));
@endphp

<div {{ $attributes->except('class')->merge(['class' => $classes !== '' ? $classes : 'empty-state']) }}>
    {{ $message }}
</div>

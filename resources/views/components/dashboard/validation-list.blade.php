@props([
    'messages' => [],
])

@php
    $items = collect($messages)
        ->flatten()
        ->filter(fn ($message) => filled($message))
        ->values()
        ->all();
    $classes = trim((string) $attributes->get('class'));
@endphp

@if ($items !== [])
    <ul {{ $attributes->except('class')->merge(['class' => $classes !== '' ? $classes : 'validation-list']) }}>
        @foreach ($items as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

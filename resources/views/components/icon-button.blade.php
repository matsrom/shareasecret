@props(['type' => 'primary', 'icon'])

@php
    $iconClasses = 'material-symbols-outlined text-2xl';
    if ($icon == 'key') {
        $iconClasses .= ' rotate-135 scale-y-[-1]';
    }
@endphp

<button
    {{ $attributes->merge(['class' => "flex w-10 h-10 items-center justify-center text-{$type} hover:bg-{$type} hover:text-white hover:rounded-md select-none"]) }}>
    <span class="{{ $iconClasses }}">
        {{ $icon ?? '' }}
    </span>
</button>

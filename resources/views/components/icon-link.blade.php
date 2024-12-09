@props(['type' => 'primary', 'icon'])

@php
    $iconClasses = 'material-symbols-outlined text-2xl';
    if ($icon == 'key') {
        $iconClasses .= ' rotate-135 scale-y-[-1]';
    }
@endphp

<a
    {{ $attributes->merge(['class' => "flex w-10 h-10 items-center justify-center text-{$type} hover:bg-{$type} hover:text-gray-700 hover:rounded-md select-none cursor-pointer"]) }}>
    <span class="{{ $iconClasses }}">
        {{ $icon ?? '' }}
    </span>
</a>

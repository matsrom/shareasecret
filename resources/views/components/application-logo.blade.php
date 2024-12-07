@props(['type' => 'white', 'class' => ''])

@if ($type == 'white')
    <img src="{{ asset('white logo.png') }}" alt="Share a Secret" class="{{ $class }}">
@else
    <img src="{{ asset('blue logo.png') }}" alt="Share a Secret" class="{{ $class }}">
@endif

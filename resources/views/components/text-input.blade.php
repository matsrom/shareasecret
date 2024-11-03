@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge([
        'class' => $disabled
            ? 'bg-gray-100 border-gray-300 focus:border-blue-700 focus:ring-blue-700 rounded-md shadow-sm'
            : 'bg-white border-gray-300 focus:border-blue-700 focus:ring-blue-700 rounded-md shadow-sm',
    ]) }}>

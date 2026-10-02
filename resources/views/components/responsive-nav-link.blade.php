@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[var(--color-primary,#3C2A21)] text-start text-base font-medium text-[var(--color-primary,#3C2A21)] bg-[var(--color-primary-soft,rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08))] focus:outline-none focus:text-[var(--theme-color-2,#25160E)] focus:bg-[var(--color-primary-soft,rgba(var(--bs-primary-rgb, 60, 42, 33), 0.12))] focus:border-[var(--theme-color-2,#25160E)] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

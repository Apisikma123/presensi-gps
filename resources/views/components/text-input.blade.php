@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[var(--color-primary,#3C2A21)] focus:ring-[var(--color-primary,#3C2A21)] rounded-md shadow-sm']) !!}>

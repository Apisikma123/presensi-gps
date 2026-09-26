@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#3C2A21] focus:ring-[#3C2A21] rounded-md shadow-sm']) !!}>

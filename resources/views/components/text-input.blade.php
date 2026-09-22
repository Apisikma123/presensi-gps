@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#1E4D3E] focus:ring-[#1E4D3E] rounded-md shadow-sm']) !!}>

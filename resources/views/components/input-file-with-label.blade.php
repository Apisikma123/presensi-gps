@props(['name' => '', 'label' => '', 'value' => null, 'accept' => 'image/jpeg,image/png,image/webp,image/jpg', 'required' => false])
<x-input-file :name="$name" :label="$label" :value="$value" :accept="$accept" :required="$required" {{ $attributes }} />

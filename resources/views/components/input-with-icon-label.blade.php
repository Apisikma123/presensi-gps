@props([
    'icon' => '',
    'name' => '',
    'label' => '',
    'placeholder' => null,
    'value' => '',
    'readonly' => false,
    'type' => 'text',
    'align' => '',
    'datepicker' => '',
    'money' => false,
    'required' => false,
    'optional' => false,
])
<div class="form-group mb-3">
    @if ($label)
        <label for="{{ $name }}" style="font-weight: 600; font-size: 13px;" class="form-label d-flex align-items-center justify-content-between mb-1">
            <span>
                {{ $label }}
                @if ($required)
                    <span class="text-danger fw-bold ms-0.5">*</span>
                @endif
            </span>
            @if ($optional && !$required)
                <span class="text-muted fw-normal font-monospace" style="font-size: 10.5px;">(Opsional)</span>
            @endif
        </label>
    @endif
    <div class="input-group input-group-merge">
        @if ($icon)
            <span class="input-group-text"><i class="{{ $icon }}"></i></span>
        @endif
        <input type="{{ $type }}" class="form-control {{ $money ? 'money' : '' }} {{ $datepicker }}"
            id="{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder ?? ($label ? 'Masukkan ' . strtolower($label) : '') }}"
            {{ $readonly ? 'readonly' : '' }} {{ $required ? 'required' : '' }} autocomplete="off" aria-autocomplete="none" value="{{ $value }}"
            style="text-align: {{ $align }}">
    </div>
</div>

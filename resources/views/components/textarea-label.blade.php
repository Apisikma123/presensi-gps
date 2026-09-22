@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => false,
    'optional' => false,
])
<div class="form-group mb-3">
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
    <textarea class="form-control" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $label }}"
        rows="2" {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
</div>

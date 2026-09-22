@props([
    'name',
    'label',
    'data',
    'key',
    'textShow',
    'selected' => '',
    'kode' => false,
    'upperCase' => false,
    'select2' => '',
    'required' => false,
    'optional' => false,
    'placeholder' => null,
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
    <select name="{{ $name }}" id="{{ $name }}" class="form-select {{ $select2 }}" {{ $required ? 'required' : '' }}>
        <option value="">{{ $placeholder ?? 'Pilih ' . $label }}</option>
        @foreach ($data as $d)
            <option {{ $d->$key == $selected ? 'selected' : '' }} value="{{ $d->$key }}">
                {{ $kode ? $d->$key . ' - ' : '' }}
                {{ $upperCase ? strtoupper(strtolower($d->$textShow)) : ucwords(strtolower($d->$textShow)) }}
            </option>
        @endforeach
    </select>
</div>

@props([
    'height' => '280px'
])

<div class="card p-3 mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="skeleton-shimmer skeleton-line" style="width: 150px; height: 16px; margin-bottom: 6px;"></div>
            <div class="skeleton-shimmer skeleton-line mb-0" style="width: 100px; height: 11px;"></div>
        </div>
        <div class="skeleton-shimmer" style="width: 90px; height: 28px; border-radius: 6px;"></div>
    </div>
    <div class="skeleton-shimmer skeleton-box d-flex align-items-end p-3 gap-2" style="height: {{ $height }}; border-radius: 8px;">
        @for ($i = 0; $i < 12; $i++)
            <div class="skeleton-shimmer flex-grow-1" style="height: {{ rand(25, 90) }}%; border-radius: 4px 4px 0 0; background: rgba(15, 23, 42, 0.06) !important;"></div>
        @endfor
    </div>
</div>

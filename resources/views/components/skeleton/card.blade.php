@props([
    'height' => '200px'
])

<div class="card p-3 mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="skeleton-shimmer skeleton-line mb-0" style="width: 140px; height: 16px;"></div>
        <div class="skeleton-shimmer" style="width: 70px; height: 26px; border-radius: 6px;"></div>
    </div>
    <div class="skeleton-shimmer skeleton-box" style="height: {{ $height }}; border-radius: 8px;"></div>
</div>

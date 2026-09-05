@props([
    'items' => 5
])

<div class="card p-3 mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="skeleton-shimmer skeleton-line mb-3" style="width: 130px; height: 16px;"></div>
    <div class="d-flex flex-column gap-3">
        @for ($i = 0; $i < $items; $i++)
            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="skeleton-shimmer skeleton-circle" style="width: 38px; height: 38px;"></div>
                    <div>
                        <div class="skeleton-shimmer skeleton-line" style="width: {{ rand(100, 160) }}px; height: 13px; margin-bottom: 5px;"></div>
                        <div class="skeleton-shimmer skeleton-line mb-0" style="width: {{ rand(60, 100) }}px; height: 10px;"></div>
                    </div>
                </div>
                <div class="skeleton-shimmer" style="width: 60px; height: 22px; border-radius: 6px;"></div>
            </div>
        @endfor
    </div>
</div>

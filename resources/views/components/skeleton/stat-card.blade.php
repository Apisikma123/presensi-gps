@props([
    'count' => 4
])

<div class="row g-3 mb-4">
    @for ($i = 0; $i < $count; $i++)
        <div class="col-xl-3 col-lg-3 col-md-6 col-12">
            <div class="card p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
                <div class="d-flex align-items-center justify-content-between">
                    <div style="flex: 1;">
                        <div class="skeleton-shimmer skeleton-line" style="width: 70px; height: 11px; margin-bottom: 8px;"></div>
                        <div class="skeleton-shimmer skeleton-line" style="width: 110px; height: 24px; margin-bottom: 6px;"></div>
                        <div class="skeleton-shimmer skeleton-line mb-0" style="width: 50px; height: 10px;"></div>
                    </div>
                    <div class="skeleton-shimmer" style="width: 44px; height: 44px; border-radius: 10px; flex-shrink: 0;"></div>
                </div>
            </div>
        </div>
    @endfor
</div>

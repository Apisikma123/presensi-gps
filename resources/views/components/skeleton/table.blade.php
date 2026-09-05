@props([
    'cols' => 5,
    'rows' => 6,
    'hasAvatar' => true,
    'hasAction' => true
])

<div class="table-responsive w-100 skeleton-table-container">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                @for ($i = 0; $i < $cols; $i++)
                    <th style="padding: 12px 16px;">
                        <div class="skeleton-shimmer skeleton-line mb-0" style="width: {{ rand(45, 80) }}px; height: 11px;"></div>
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @for ($r = 0; $r < $rows; $r++)
                <tr>
                    @for ($c = 0; $c < $cols; $c++)
                        <td style="padding: 14px 16px;">
                            @if ($c === 0 && $hasAvatar)
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="skeleton-shimmer skeleton-circle" style="width: 34px; height: 34px;"></div>
                                    <div>
                                        <div class="skeleton-shimmer skeleton-line" style="width: {{ rand(90, 140) }}px; height: 13px; margin-bottom: 5px;"></div>
                                        <div class="skeleton-shimmer skeleton-line mb-0" style="width: {{ rand(60, 90) }}px; height: 10px;"></div>
                                    </div>
                                </div>
                            @elseif ($c === $cols - 1 && $hasAction)
                                <div class="d-flex align-items-center gap-1.5 justify-content-end">
                                    <div class="skeleton-shimmer" style="width: 30px; height: 30px; border-radius: 8px;"></div>
                                </div>
                            @elseif ($c % 3 === 0)
                                <div class="skeleton-shimmer" style="width: {{ rand(50, 75) }}px; height: 20px; border-radius: 6px;"></div>
                            @else
                                <div class="skeleton-shimmer skeleton-line mb-0" style="width: {{ rand(60, 120) }}px; height: 12px;"></div>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
</div>

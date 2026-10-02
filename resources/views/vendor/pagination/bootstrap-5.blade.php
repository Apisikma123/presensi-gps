@if ($paginator->total() > 0)
    <nav class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
        <div class="text-muted" style="font-size: 12.5px;">
            Menampilkan <span class="fw-bold text-dark font-mono">{{ $paginator->firstItem() ?? 1 }}</span> - <span class="fw-bold text-dark font-mono">{{ $paginator->lastItem() ?? $paginator->total() }}</span> dari <span class="fw-bold text-dark font-mono">{{ number_format($paginator->total(), 0, ',', '.') }}</span> data
        </div>

        @if ($paginator->hasPages())
            <ul class="pagination pagination-sm mb-0 align-items-center gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 13px; border: 1px solid rgba(15, 23, 42, 0.08); background-color: #F8FAFC !important; color: #CBD5E1 !important;">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 13px; border: 1px solid rgba(15, 23, 42, 0.12); background-color: #FFFFFF !important; color: #475569 !important; text-decoration: none;">&lsaquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" style="border-radius: 8px; min-width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 1px solid rgba(15, 23, 42, 0.08); background-color: #F8FAFC !important; color: #94A3B8 !important;">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link fw-bold font-mono" style="border-radius: 8px; min-width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 12.5px; background-color: var(--color-primary, var(--theme-color-1, #1B365D)) !important; border: 1px solid var(--color-primary, var(--theme-color-1, #1B365D)) !important; color: #FFFFFF !important; box-shadow: 0 2px 8px var(--color-primary-soft, rgba(27,54,93,0.25)) !important;">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link font-mono" href="{{ $url }}" style="border-radius: 8px; min-width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 12.5px; border: 1px solid rgba(15, 23, 42, 0.12); background-color: #FFFFFF !important; color: #475569 !important; text-decoration: none;">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 13px; border: 1px solid rgba(15, 23, 42, 0.12); background-color: #FFFFFF !important; color: #475569 !important; text-decoration: none;">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 13px; border: 1px solid rgba(15, 23, 42, 0.08); background-color: #F8FAFC !important; color: #CBD5E1 !important;">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        @endif
    </nav>
@endif

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
                        <span class="page-link" style="border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px; border-color: #E2E8F0; color: #94A3B8;">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px; border-color: #E2E8F0; color: #475569;">&lsaquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link" style="border-radius: 6px; min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12px; border-color: #E2E8F0; color: #94A3B8;">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link fw-bold font-mono" style="border-radius: 6px; min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12.5px; background-color: var(--theme-color-1, #1E4D3E); border-color: var(--theme-color-1, #1E4D3E); color: #FFFFFF;">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link font-mono" href="{{ $url }}" style="border-radius: 6px; min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 12.5px; border-color: #E2E8F0; color: #475569;">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px; border-color: #E2E8F0; color: #475569;">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" style="border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px; border-color: #E2E8F0; color: #94A3B8;">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        @endif
    </nav>
@endif

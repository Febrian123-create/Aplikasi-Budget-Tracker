@if ($paginator->hasPages())
    <nav class="bunrek-pagination" role="navigation" aria-label="Pagination Navigation" style="display: flex; align-items: center; justify-content: center; gap: var(--space-xs); margin-top: var(--space-lg); padding: var(--space-md) var(--space-lg); border-top: 1px solid var(--border-light);">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); border: 1.5px solid var(--border-color); color: var(--text-light); cursor: not-allowed; opacity: 0.5;">
                <i class="bi bi-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="pagination-item link" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); border: 1.5px solid var(--border-color); color: var(--text-body); background: var(--bg-white); transition: var(--transition-fast);" onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)'; this.style.background='var(--primary-50)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-body)'; this.style.background='var(--bg-white)'">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="pagination-item disabled" aria-disabled="true" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; color: var(--text-light);">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-item active" aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; font-weight: 600; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-item link" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); border: 1.5px solid var(--border-color); color: var(--text-body); background: var(--bg-white); transition: var(--transition-fast);" onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)'; this.style.background='var(--primary-50)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-body)'; this.style.background='var(--bg-white)'">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" class="pagination-item link" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); border: 1.5px solid var(--border-color); color: var(--text-body); background: var(--bg-white); transition: var(--transition-fast);" onmouseover="this.style.borderColor='var(--primary-color)'; this.style.color='var(--primary-color)'; this.style.background='var(--primary-50)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-body)'; this.style.background='var(--bg-white)'">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="pagination-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: var(--radius-md); border: 1.5px solid var(--border-color); color: var(--text-light); cursor: not-allowed; opacity: 0.5;">
                <i class="bi bi-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif

@if ($paginator->hasPages())
        <div class="paginador">
        [
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
        @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></a>
        @endif
        
        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link">[{{ $page }}]</span>
                    @else
                        <a class="page-link" href="{{ $url }}">[{{ $page }}]</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        
        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></a>
        @else
                <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
        @endif
        ]
        </div>
@endif

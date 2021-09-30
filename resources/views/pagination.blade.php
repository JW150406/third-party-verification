@if ($paginator->hasPages())
<div class="dataTables_wrapper">
<div class="dataTables_paginate paging_simple_numbers"  style="display: block;">
	{{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
	<a class="paginate_button previous disabled" aria-controls="lead-table" data-dt-idx="0" tabindex="0" >Previous</a>
	@else
	<a href="{{ $paginator->previousPageUrl() }}" class="paginate_button previous " aria-controls="lead-table" data-dt-idx="0" tabindex="0" >Previous</a>
	@endif
	<span>
		{{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
            	<span class="ellipsis">…</span>
            @endif
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
						<a class="paginate_button current" aria-controls="lead-table" data-dt-idx="1" tabindex="0">{{ $page }}</a>
					@else
						<a href="{{ $url }}" class="paginate_button" aria-controls="lead-table" data-dt-idx="1" tabindex="0">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
	</span>
	{{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
	<a href="{{ $paginator->nextPageUrl() }}" class="paginate_button next" aria-controls="lead-table" data-dt-idx="7" tabindex="0">Next</a>
	@else
	<a  class="paginate_button next disabled" aria-controls="lead-table" data-dt-idx="7" tabindex="0" >Next</a>
	@endif
</div>
</div>
@endif
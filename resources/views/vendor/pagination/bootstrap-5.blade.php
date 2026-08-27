@if ($paginator->hasPages())
    <nav class="app-pagination" aria-label="{{ __('Pagination Navigation') }}">
        <div class="pagination-summary">
            <span class="small text-muted">
                {{ __('Showing') }}
                <strong>{{ $paginator->firstItem() }}</strong>
                {{ __('to') }}
                <strong>{{ $paginator->lastItem() }}</strong>
                {{ __('of') }}
                <strong>{{ $paginator->total() }}</strong>
                {{ __('results') }}
            </span>
        </div>
        <div class="pagination-nav pagination-nav-previous">
            <ul class="pagination mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">@lang('pagination.previous')</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
                </li>
            @endif
            </ul>
        </div>

        <div class="pagination-numbers">
            <ul class="pagination mb-0">
                @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
                @endforeach
            </ul>
        </div>

        <div class="pagination-nav pagination-nav-next">
            <ul class="pagination mb-0">
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">@lang('pagination.next')</span>
                </li>
            @endif
            </ul>
        </div>
    </nav>
@endif

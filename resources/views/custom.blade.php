@if ($paginator->hasPages())
    <nav class="pagination-nav" aria-label="transaction pagination">

        @if ($paginator->onFirstPage())
            <span class="page-btn disabled">Previous</span>
        @else
            <a class="page-btn" href="{{ $paginator->previousPageUrl() }}">
                Previous
            </a>
        @endif

        <span class="page-info">
            Page {{ $paginator->currentPage() }}
            of {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a class="page-btn" href="{{ $paginator->nextPageUrl() }}">
                Next
            </a>
        @else
            <span class="page-btn disabled">Next</span>
        @endif

    </nav>

@endif

@if ($paginator->hasPages())
    <nav class="manage-pagination" role="navigation" aria-label="Navigasi halaman">
        <p class="manage-pagination__summary">
            Menampilkan
            @if ($paginator->firstItem())
                <strong>{{ $paginator->firstItem() }}</strong>
                sampai
                <strong>{{ $paginator->lastItem() }}</strong>
            @else
                <strong>{{ $paginator->count() }}</strong>
            @endif
            dari
            <strong>{{ $paginator->total() }}</strong>
            data
        </p>

        <div class="manage-pagination__controls">
            @if ($paginator->onFirstPage())
                <span class="manage-pagination__button is-disabled" aria-disabled="true">
                    <span class="manage-pagination__arrow" aria-hidden="true">&lsaquo;</span>
                    <span class="manage-pagination__button-label">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="manage-pagination__button">
                    <span class="manage-pagination__arrow" aria-hidden="true">&lsaquo;</span>
                    <span class="manage-pagination__button-label">Sebelumnya</span>
                </a>
            @endif

            <div class="manage-pagination__pages">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="manage-pagination__ellipsis" aria-disabled="true">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="manage-pagination__current" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="manage-pagination__link" aria-label="Buka halaman {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="manage-pagination__button">
                    <span class="manage-pagination__button-label">Berikutnya</span>
                    <span class="manage-pagination__arrow" aria-hidden="true">&rsaquo;</span>
                </a>
            @else
                <span class="manage-pagination__button is-disabled" aria-disabled="true">
                    <span class="manage-pagination__button-label">Berikutnya</span>
                    <span class="manage-pagination__arrow" aria-hidden="true">&rsaquo;</span>
                </span>
            @endif
        </div>
    </nav>
@endif

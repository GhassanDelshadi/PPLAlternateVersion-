@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 w-100 my-2" aria-label="Navigasi Halaman">
        <div class="small text-muted">
            Menampilkan
            <span class="fw-semibold text-dark">{{ $paginator->firstItem() ?? 0 }}</span>
            sampai
            <span class="fw-semibold text-dark">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="fw-semibold text-dark">{{ $paginator->total() }}</span>
            data
        </div>

        <ul class="pagination pagination-sm mb-0 flex-wrap justify-content-center">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link rounded-pill-start px-3 text-muted">
                        <i class="bi bi-chevron-left me-1"></i> Sebelumnya
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-pill-start px-3" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left me-1"></i> Sebelumnya
                    </a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link px-3">{{ $element }}</span></li>
                @endif

                {{-- Array Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link px-3 fw-bold" style="background-color: var(--app-primary, #005954) !important; border-color: var(--app-primary, #005954) !important; color: #ffffff !important;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link px-3" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill-end px-3" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Berikutnya <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link rounded-pill-end px-3 text-muted">
                        Berikutnya <i class="bi bi-chevron-right ms-1"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

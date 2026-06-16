@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-[#6f6f6f]">
            @if ($paginator->firstItem())
                Mostrando <span class="font-semibold text-[#323232]">{{ $paginator->firstItem() }}</span> até <span class="font-semibold text-[#323232]">{{ $paginator->lastItem() }}</span> de <span class="font-semibold text-[#323232]">{{ $paginator->total() }}</span> registros
            @else
                Nenhum registro encontrado
            @endif
        </div>

        <div class="flex items-center justify-between gap-2 sm:justify-end">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Página anterior" class="inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-md border border-[#e5e0dc] bg-[#f7f5f3] px-3 text-sm font-semibold text-[#b0aaa4]">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior" class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-[#e5e0dc] bg-counter-bg px-3 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3] hover:text-[#323232]">
                    Anterior
                </a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="inline-flex size-9 items-center justify-center rounded-md text-sm font-semibold text-[#8f8f8f]">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex size-9 items-center justify-center rounded-md bg-counter-primary text-sm font-semibold text-white shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="Ir para página {{ $page }}" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] bg-counter-bg text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3] hover:text-[#323232]">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Próxima página" class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-[#e5e0dc] bg-counter-bg px-3 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3] hover:text-[#323232]">
                    Próxima
                </a>
            @else
                <span aria-disabled="true" aria-label="Próxima página" class="inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-md border border-[#e5e0dc] bg-[#f7f5f3] px-3 text-sm font-semibold text-[#b0aaa4]">
                    Próxima
                </span>
            @endif
        </div>
    </nav>
@endif

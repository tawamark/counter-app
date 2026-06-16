<x-layouts.app title="Divergências">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Divergências</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Compare o saldo do sistema com as quantidades contadas fisicamente.</p>
        </div>
    </div>

    <section class="mb-4 grid gap-4 md:grid-cols-3">
        <a href="{{ route('divergences.index', ['type' => 'shortage']) }}" class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm transition hover:border-counter-primary">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Falta física</p>
                <i data-lucide="trending-down" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ (int) ($summary->shortages ?? 0) }}</p>
        </a>

        <a href="{{ route('divergences.index', ['type' => 'surplus']) }}" class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm transition hover:border-counter-primary">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Sobra física</p>
                <i data-lucide="trending-up" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ (int) ($summary->surpluses ?? 0) }}</p>
        </a>

        <a href="{{ route('divergences.index', ['type' => 'none']) }}" class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm transition hover:border-counter-primary">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Sem divergência</p>
                <i data-lucide="circle-check" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ (int) ($summary->matches ?? 0) }}</p>
        </a>
    </section>

    <form method="GET" action="{{ route('divergences.index') }}" class="mb-4 rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <x-dropdown-select name="type" label="Tipo" :selected="$filters['type'] ?? ''" :options="['' => 'Todos', 'shortage' => 'Falta física', 'surplus' => 'Sobra física', 'none' => 'Sem divergência']" class="sm:w-72" />

            <div class="flex flex-col-reverse gap-3 sm:flex-row">
                <a href="{{ route('divergences.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
                    Limpar
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                    <i data-lucide="search" class="size-4"></i>
                    Filtrar
                </button>
            </div>
        </div>
    </form>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($items->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="scale" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhum item encontrado</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Registre quantidades contadas para visualizar faltas, sobras e itens sem divergência.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[960px] text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Produto</th>
                            <th class="px-4 py-3 font-semibold">Contagem</th>
                            <th class="px-4 py-3 font-semibold">Sistema</th>
                            <th class="px-4 py-3 font-semibold">Contado</th>
                            <th class="px-4 py-3 font-semibold">Diferença</th>
                            <th class="px-4 py-3 font-semibold">Tipo</th>
                            <th class="px-4 py-3 text-right font-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->product?->name ?? 'Produto removido' }}</div>
                                    <div class="text-xs text-[#6f6f6f]">SKU {{ $item->product?->sku ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $item->inventoryCount->title }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $item->system_quantity, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $item->counted_quantity, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 font-medium {{ (float) $item->difference === 0.0 ? 'text-[#6f6f6f]' : 'text-counter-primary' }}">{{ number_format((float) $item->difference, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">
                                    @if ((float) $item->difference < 0)
                                        Falta física
                                    @elseif ((float) $item->difference > 0)
                                        Sobra física
                                    @else
                                        Sem divergência
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end">
                                        <a href="{{ route('inventory-counts.show', $item->inventoryCount) }}" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" title="Ver contagem">
                                            <i data-lucide="eye" class="size-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#e5e0dc] px-4 py-3">
                {{ $items->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

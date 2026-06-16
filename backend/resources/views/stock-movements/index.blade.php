<x-layouts.app title="Movimentações">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Movimentações</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Acompanhe entradas, saídas e ajustes de estoque.</p>
        </div>
        <a href="{{ route('stock-movements.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="plus" class="size-4"></i>
            Nova movimentação
        </a>
    </div>

    <form method="GET" action="{{ route('stock-movements.index') }}" class="mb-4 rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
        <div class="grid gap-4 md:grid-cols-5">
            <x-dropdown-select name="product_id" label="Produto" :selected="$filters['product_id'] ?? ''" :options="collect(['' => 'Todos'])->union($products->pluck('name', 'id'))->all()" />

            <x-dropdown-select name="type" label="Tipo" :selected="$filters['type'] ?? ''" :options="['' => 'Todos', 'entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste']" />

            <x-dropdown-select name="user_id" label="Usuário" :selected="$filters['user_id'] ?? ''" :options="collect(['' => 'Todos'])->union($users->pluck('name', 'id'))->all()" />

            <div>
                <label for="date_from" class="mb-1 block text-sm font-medium">De</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            </div>

            <div>
                <label for="date_to" class="mb-1 block text-sm font-medium">Até</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            </div>
        </div>

        <div class="mt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
                Limpar
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                <i data-lucide="search" class="size-4"></i>
                Filtrar
            </button>
        </div>
    </form>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($movements->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="arrow-down-up" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhuma movimentação encontrada</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Registre uma movimentação ou ajuste os filtros para ampliar a busca.</p>
                <a href="{{ route('stock-movements.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                    <i data-lucide="plus" class="size-4"></i>
                    Registrar movimentação
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Produto</th>
                            <th class="px-4 py-3 font-semibold">Tipo</th>
                            <th class="px-4 py-3 font-semibold">Quantidade</th>
                            <th class="px-4 py-3 font-semibold">Saldo antes</th>
                            <th class="px-4 py-3 font-semibold">Saldo depois</th>
                            <th class="px-4 py-3 font-semibold">Usuário</th>
                            <th class="px-4 py-3 font-semibold">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($movements as $movement)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $movement->product?->name ?? 'Produto removido' }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ ['entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste'][$movement->type] ?? $movement->type }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $movement->quantity, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $movement->quantity_before, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $movement->quantity_after, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $movement->user?->name ?? 'Usuário removido' }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#e5e0dc] px-4 py-3">
                {{ $movements->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

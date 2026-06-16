<x-layouts.app :title="$count->title">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('inventory-counts.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
                <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
                Voltar para contagens
            </a>
            <h1 class="mt-3 text-2xl font-semibold">{{ $count->title }}</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Saldo congelado em {{ $count->started_at?->format('d/m/Y H:i') ?? '-' }}.</p>
        </div>
        <span class="inline-flex w-fit rounded-full bg-orange-50 px-3 py-1 text-sm font-medium text-counter-primary">
            {{ ['open' => 'Aberta', 'in_progress' => 'Em andamento', 'finished' => 'Finalizada', 'approved' => 'Aprovada'][$count->status] ?? $count->status }}
        </span>
    </div>

    <section class="mb-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <p class="text-sm text-[#6f6f6f]">Produtos</p>
            <p class="mt-1 text-2xl font-semibold">{{ $count->items->count() }}</p>
        </div>
        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <p class="text-sm text-[#6f6f6f]">Criada por</p>
            <p class="mt-1 text-lg font-semibold">{{ $count->creator?->name ?? 'Usuário removido' }}</p>
        </div>
        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <p class="text-sm text-[#6f6f6f]">Status</p>
            <p class="mt-1 text-lg font-semibold">{{ ['open' => 'Aberta', 'in_progress' => 'Em andamento', 'finished' => 'Finalizada', 'approved' => 'Aprovada'][$count->status] ?? $count->status }}</p>
        </div>
    </section>

    <form method="POST" action="{{ route('inventory-counts.items.update', $count) }}" class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @csrf

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Produto</th>
                        <th class="px-4 py-3 font-semibold">SKU</th>
                        <th class="px-4 py-3 font-semibold">Saldo do sistema</th>
                        <th class="px-4 py-3 font-semibold">Quantidade contada</th>
                        <th class="px-4 py-3 font-semibold">Diferença</th>
                        <th class="px-4 py-3 font-semibold">Contado por</th>
                        <th class="px-4 py-3 font-semibold">Sincronização</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e0dc]">
                    @foreach ($count->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $item->product?->name ?? 'Produto removido' }}</td>
                            <td class="px-4 py-3 text-[#6f6f6f]">{{ $item->product?->sku ?? '-' }}</td>
                            <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $item->system_quantity, 3, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <input name="items[{{ $loop->index }}][counted_quantity]" type="number" value="{{ old("items.{$loop->index}.counted_quantity", $item->counted_quantity) }}" min="0" step="0.001" class="block w-32 rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100" @disabled(in_array($count->status, ['finished', 'approved'], true))>
                                @error("items.{$loop->index}.counted_quantity")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $item->difference, 3, ',', '.') }}</td>
                            <td class="px-4 py-3 text-[#6f6f6f]">{{ $item->counter?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-[#6f6f6f]">{{ ['pending' => 'Pendente', 'synced' => 'Sincronizada', 'error' => 'Erro'][$item->sync_status] ?? $item->sync_status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-[#e5e0dc] px-4 py-3 sm:flex-row sm:justify-end">
            <a href="{{ route('inventory-counts.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
                Voltar
            </a>
            <button type="submit" @disabled(in_array($count->status, ['finished', 'approved'], true)) class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16] disabled:cursor-not-allowed disabled:bg-[#d8d2cc]">
                <i data-lucide="save" class="size-4"></i>
                Salvar contagem
            </button>
        </div>
    </form>

    @if ($count->status === 'approved' || $count->adjustmentMovements->isNotEmpty())
        <section class="mt-4 rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
            <div class="border-b border-[#e5e0dc] px-4 py-3">
                <h2 class="text-sm font-semibold">Ajustes aprovados</h2>
                <p class="mt-1 text-sm text-[#6f6f6f]">Movimentações de ajuste geradas pela aprovação desta contagem.</p>
            </div>

            @if ($count->adjustmentMovements->isEmpty())
                <div class="px-4 py-6 text-sm text-[#6f6f6f]">
                    Nenhum ajuste foi necessário.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[780px] text-left text-sm">
                        <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Produto</th>
                                <th class="px-4 py-3 font-semibold">Saldo anterior</th>
                                <th class="px-4 py-3 font-semibold">Saldo aprovado</th>
                                <th class="px-4 py-3 font-semibold">Responsável</th>
                                <th class="px-4 py-3 font-semibold">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e5e0dc]">
                            @foreach ($count->adjustmentMovements as $movement)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $movement->product?->name ?? 'Produto removido' }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $movement->quantity_before, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $movement->quantity_after, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ $movement->user?->name ?? 'Usuário removido' }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ $movement->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif

    @if (auth()->user()->role === 'admin')
        <section class="mt-4 flex flex-col gap-3 rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold">Aprovação de ajustes</h2>
                <p class="mt-1 text-sm text-[#6f6f6f]">Finalize a contagem e aprove os ajustes para atualizar o estoque com as quantidades contadas.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                @if (! in_array($count->status, ['finished', 'approved'], true))
                    <form method="POST" action="{{ route('inventory-counts.finish', $count) }}">
                        @csrf
                        <button type="button" x-on:click="$dispatch('open-confirm-modal', { title: 'Finalizar contagem', message: 'Após finalizar, os itens não poderão ser alterados antes da aprovação. Deseja continuar?', confirmText: 'Finalizar', tone: 'primary', onConfirm: () => $el.closest('form').submit() })" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#323232] transition hover:bg-[#f7f5f3]">
                            <i data-lucide="check" class="size-4"></i>
                            Finalizar contagem
                        </button>
                    </form>
                @endif

                @if ($count->status === 'finished')
                    <form method="POST" action="{{ route('inventory-counts.approve', $count) }}">
                        @csrf
                        <button type="button" x-on:click="$dispatch('open-confirm-modal', { title: 'Aprovar ajustes', message: 'A aprovação atualizará o estoque com as quantidades contadas e gerará movimentações de ajuste. Deseja continuar?', confirmText: 'Aprovar', tone: 'primary', onConfirm: () => $el.closest('form').submit() })" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                            <i data-lucide="badge-check" class="size-4"></i>
                            Aprovar ajustes
                        </button>
                    </form>
                @endif
            </div>
        </section>
    @endif
</x-layouts.app>

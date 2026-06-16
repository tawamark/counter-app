<x-layouts.app title="Contagens">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Contagens</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Crie conferências de estoque e acompanhe os produtos vinculados.</p>
        </div>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('inventory-counts.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                <i data-lucide="plus" class="size-4"></i>
                Nova contagem
            </a>
        @endif
    </div>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($counts->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="clipboard-list" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhuma contagem cadastrada</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Crie uma contagem para congelar o saldo atual dos produtos selecionados.</p>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('inventory-counts.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                        <i data-lucide="plus" class="size-4"></i>
                        Criar contagem
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Título</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Produtos</th>
                            <th class="px-4 py-3 font-semibold">Criada por</th>
                            <th class="px-4 py-3 font-semibold">Início</th>
                            <th class="px-4 py-3 text-right font-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($counts as $count)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $count->title }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ ['open' => 'Aberta', 'in_progress' => 'Em andamento', 'finished' => 'Finalizada', 'approved' => 'Aprovada'][$count->status] ?? $count->status }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $count->items_count }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $count->creator?->name ?? 'Usuário removido' }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $count->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end">
                                        <a href="{{ route('inventory-counts.show', $count) }}" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" title="Ver detalhes">
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
                {{ $counts->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

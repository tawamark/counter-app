<x-layouts.app title="Relatórios">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Relatórios</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Exporte dados de estoque, movimentações e divergências em CSV.</p>
        </div>
    </div>

    <section class="mb-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Produtos cadastrados</p>
                <i data-lucide="package" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ (int) ($stockSummary->products_count ?? 0) }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">{{ number_format((float) ($stockSummary->quantity_sum ?? 0), 3, ',', '.') }} unidades em estoque</p>
        </div>

        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Valor de custo</p>
                <i data-lucide="coins" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">R$ {{ number_format((float) ($stockSummary->cost_value ?? 0), 2, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">Baseado no saldo atual</p>
        </div>

        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Movimentações</p>
                <i data-lucide="arrow-down-up" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ (int) ($movementSummary->movements_count ?? 0) }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">{{ (int) ($movementSummary->entries_count ?? 0) }} entradas, {{ (int) ($movementSummary->exits_count ?? 0) }} saídas e {{ (int) ($movementSummary->adjustments_count ?? 0) }} ajustes</p>
        </div>
    </section>

    <section class="space-y-4">
        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
            <div class="border-b border-[#e5e0dc] px-4 py-3">
                <h2 class="text-sm font-semibold">Estoque atual</h2>
                <p class="mt-1 text-sm text-[#6f6f6f]">Produtos com SKU, categoria, fornecedor, unidade, quantidade e preços.</p>
            </div>
            <div class="p-4">
                <a href="{{ route('reports.stock') }}" data-no-loader class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                    <i data-lucide="download" class="size-4"></i>
                    Exportar estoque
                </a>
            </div>
        </div>

        <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
            <div class="border-b border-[#e5e0dc] px-4 py-3">
                <h2 class="text-sm font-semibold">Movimentações</h2>
                <p class="mt-1 text-sm text-[#6f6f6f]">Entradas, saídas, ajustes, responsáveis, motivos e contagens vinculadas.</p>
            </div>
            <form method="GET" action="{{ route('reports.movements') }}" data-no-loader class="p-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <x-dropdown-select name="product_id" label="Produto" :options="collect(['' => 'Todos'])->union($products->pluck('name', 'id'))->all()" />

                    <x-dropdown-select name="type" id="movement_type" label="Tipo" :options="['' => 'Todos', 'entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste']" />

                    <x-dropdown-select name="user_id" label="Usuário" :options="collect(['' => 'Todos'])->union($users->pluck('name', 'id'))->all()" />

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="date_from" class="mb-1 block text-sm font-medium">De</label>
                            <input id="date_from" name="date_from" type="date" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                        </div>
                        <div>
                            <label for="date_to" class="mb-1 block text-sm font-medium">Até</label>
                            <input id="date_to" name="date_to" type="date" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                        <i data-lucide="download" class="size-4"></i>
                        Exportar movimentações
                    </button>
                </div>
            </form>
        </div>

        @if (auth()->user()->role === 'admin')
            <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
                <div class="border-b border-[#e5e0dc] px-4 py-3">
                    <h2 class="text-sm font-semibold">Divergências</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Itens contados com falta física, sobra física ou sem divergência.</p>
                </div>
                <div class="grid gap-4 p-4 md:grid-cols-[1fr_auto] md:items-end">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-md border border-[#e5e0dc] p-3">
                            <p class="text-sm text-[#6f6f6f]">Faltas físicas</p>
                            <p class="mt-1 text-xl font-semibold">{{ (int) ($divergenceSummary->shortages ?? 0) }}</p>
                        </div>
                        <div class="rounded-md border border-[#e5e0dc] p-3">
                            <p class="text-sm text-[#6f6f6f]">Sobras físicas</p>
                            <p class="mt-1 text-xl font-semibold">{{ (int) ($divergenceSummary->surpluses ?? 0) }}</p>
                        </div>
                        <div class="rounded-md border border-[#e5e0dc] p-3">
                            <p class="text-sm text-[#6f6f6f]">Sem divergência</p>
                            <p class="mt-1 text-xl font-semibold">{{ (int) ($divergenceSummary->matches ?? 0) }}</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('reports.divergences') }}" data-no-loader class="flex flex-col gap-3 sm:flex-row">
                        <x-dropdown-select name="type" id="divergence_type" :options="['' => 'Todos', 'shortage' => 'Falta física', 'surplus' => 'Sobra física', 'none' => 'Sem divergência']" class="sm:w-52" />
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                            <i data-lucide="download" class="size-4"></i>
                            Exportar divergências
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>

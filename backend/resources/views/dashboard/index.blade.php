<x-layouts.app title="Dashboard">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Dashboard</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Resumo operacional do estoque, contagens e movimentações.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if (in_array(auth()->user()->role, ['admin', 'stockist'], true))
                <a href="{{ route('stock-movements.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                    <i data-lucide="plus" class="size-4"></i>
                    Movimentar estoque
                </a>
            @endif

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('inventory-counts.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-[#e5e0dc] bg-counter-bg px-4 py-2.5 text-sm font-semibold text-[#323232] transition hover:bg-orange-50 hover:text-counter-primary">
                    <i data-lucide="clipboard-list" class="size-4"></i>
                    Nova contagem
                </a>
            @endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Produtos</p>
                <i data-lucide="package" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ $totalProducts }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">{{ $totalCategories }} categorias cadastradas</p>
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Fornecedores</p>
                <i data-lucide="truck" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ $totalSuppliers }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">Base de fornecimento</p>
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Contagens abertas</p>
                <i data-lucide="clipboard-list" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ $openInventoryCounts }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">Abertas ou em andamento</p>
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-[#6f6f6f]">Divergências</p>
                <i data-lucide="scale" class="size-5 text-counter-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ $shortageItems + $surplusItems }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">{{ $shortageItems }} faltas e {{ $surplusItems }} sobras</p>
        </section>
    </div>

    <div class="mt-6 grid gap-4 xl:grid-cols-3">
        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold">Movimentações por tipo</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Distribuição das operações registradas.</p>
                </div>
                <i data-lucide="bar-chart-3" class="size-5 text-counter-primary"></i>
            </div>

            <div class="mt-5 space-y-4">
                @foreach ($movementChart as $item)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium">{{ $item['label'] }}</span>
                            <span class="text-[#6f6f6f]">{{ $item['value'] }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-[#f0ebe7]">
                            <div class="h-full rounded-full {{ $item['class'] }}" style="width: {{ ($item['value'] / $movementTotal) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold">Status das contagens</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Situação atual dos inventários.</p>
                </div>
                <i data-lucide="circle-check" class="size-5 text-counter-primary"></i>
            </div>

            <div class="mt-5 space-y-4">
                @foreach ($countStatusChart as $item)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium">{{ $item['label'] }}</span>
                            <span class="text-[#6f6f6f]">{{ $item['value'] }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-[#f0ebe7]">
                            <div class="h-full rounded-full {{ $item['class'] }}" style="width: {{ ($item['value'] / $countStatusTotal) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold">Últimos 7 dias</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Volume diário de movimentações.</p>
                </div>
                <i data-lucide="trending-up" class="size-5 text-counter-primary"></i>
            </div>

            <div class="mt-5 flex h-36 items-end gap-2">
                @foreach ($recentMovementDays as $day)
                    <div class="flex h-full flex-1 flex-col justify-end gap-2">
                        <div class="flex flex-1 items-end">
                            <div class="w-full rounded-t-md bg-counter-primary" style="height: {{ max(6, ($day['value'] / $recentMovementDaysMax) * 100) }}%"></div>
                        </div>
                        <div class="text-center text-xs text-[#6f6f6f]">{{ $day['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Estoque por categoria</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Maiores volumes em estoque por grupo de produto.</p>
                </div>
                <i data-lucide="tags" class="size-5 text-counter-primary"></i>
            </div>

            @if ($stockByCategory->isEmpty())
                <div class="mt-6 flex min-h-40 items-center justify-center rounded-md border border-dashed border-[#d8d2cc]">
                    <p class="text-sm text-[#6f6f6f]">Nenhum produto cadastrado.</p>
                </div>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($stockByCategory as $item)
                        <div>
                            <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                <span class="truncate font-medium">{{ $item['label'] }}</span>
                                <span class="shrink-0 text-[#6f6f6f]">{{ number_format((float) $item['value'], 3, ',', '.') }}</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-[#f0ebe7]">
                                <div class="h-full rounded-full bg-counter-primary" style="width: {{ ($item['value'] / $stockByCategoryMax) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Estoque baixo</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Produtos com saldo igual ou abaixo de 5 unidades.</p>
                </div>
                <i data-lucide="alert-triangle" class="size-5 text-counter-primary"></i>
            </div>

            @if ($lowStockProducts->isEmpty())
                <div class="mt-6 flex min-h-40 items-center justify-center rounded-md border border-dashed border-[#d8d2cc]">
                    <p class="text-sm text-[#6f6f6f]">Nenhum produto em estoque baixo.</p>
                </div>
            @else
                <div class="mt-6 overflow-hidden rounded-md border border-[#e5e0dc]">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Produto</th>
                                <th class="px-4 py-3 font-semibold">SKU</th>
                                <th class="px-4 py-3 font-semibold">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e5e0dc]">
                            @foreach ($lowStockProducts as $product)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ $product->sku }}</td>
                                    <td class="px-4 py-3 text-counter-primary">{{ number_format((float) $product->current_quantity, 3, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Contagens recentes</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">Acompanhe os processos de conferência mais recentes.</p>
                </div>
                <i data-lucide="clipboard-list" class="size-5 text-counter-primary"></i>
            </div>

            @if ($recentInventoryCounts->isEmpty())
                <div class="mt-6 flex min-h-40 items-center justify-center rounded-md border border-dashed border-[#d8d2cc]">
                    <p class="text-sm text-[#6f6f6f]">Nenhuma contagem cadastrada.</p>
                </div>
            @else
                <div class="mt-6 overflow-hidden rounded-md border border-[#e5e0dc]">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Título</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Criada por</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e5e0dc]">
                            @foreach ($recentInventoryCounts as $count)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $count->title }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ ['open' => 'Aberta', 'in_progress' => 'Em andamento', 'finished' => 'Finalizada', 'approved' => 'Aprovada'][$count->status] ?? $count->status }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ $count->creator?->name ?? 'Usuário removido' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Movimentações recentes</h2>
                    <p class="mt-1 text-sm text-[#6f6f6f]">As últimas operações de estoque aparecem aqui.</p>
                </div>
                <i data-lucide="arrow-down-up" class="size-5 text-counter-primary"></i>
            </div>

            @if ($recentMovements->isEmpty())
                <div class="mt-6 flex min-h-40 items-center justify-center rounded-md border border-dashed border-[#d8d2cc]">
                    <p class="text-sm text-[#6f6f6f]">Nenhuma movimentação registrada.</p>
                </div>
            @else
                <div class="mt-6 overflow-hidden rounded-md border border-[#e5e0dc]">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Produto</th>
                                <th class="px-4 py-3 font-semibold">Tipo</th>
                                <th class="px-4 py-3 font-semibold">Quantidade</th>
                                <th class="px-4 py-3 font-semibold">Usuário</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e5e0dc]">
                            @foreach ($recentMovements as $movement)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $movement->product?->name ?? 'Produto removido' }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ ['entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste'][$movement->type] ?? $movement->type }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $movement->quantity, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-[#6f6f6f]">{{ $movement->user?->name ?? 'Usuário removido' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>

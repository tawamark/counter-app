<x-layouts.app title="Produtos">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Produtos</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Gerencie os itens do estoque com SKU, código de barras, preços e saldo atual.</p>
        </div>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                <i data-lucide="plus" class="size-4"></i>
                Novo produto
            </a>
        @endif
    </div>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($products->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="package" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhum produto cadastrado</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Cadastre produtos para registrar movimentações, contagens e divergências.</p>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('products.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                        <i data-lucide="plus" class="size-4"></i>
                        Cadastrar produto
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Produto</th>
                            <th class="px-4 py-3 font-semibold">SKU</th>
                            <th class="px-4 py-3 font-semibold">Categoria</th>
                            <th class="px-4 py-3 font-semibold">Fornecedor</th>
                            <th class="px-4 py-3 font-semibold">Quantidade</th>
                            <th class="px-4 py-3 font-semibold">Preço venda</th>
                            @if (auth()->user()->role === 'admin')
                                <th class="px-4 py-3 text-right font-semibold">Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $product->name }}</div>
                                    <div class="text-xs text-[#6f6f6f]">{{ $product->barcode ?: 'Sem código de barras' }}</div>
                                </td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $product->sku }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $product->category?->name ?? 'Sem categoria' }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $product->supplier?->name ?? 'Sem fornecedor' }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ number_format((float) $product->current_quantity, 3, ',', '.') }} {{ $product->unit }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</td>
                                @if (auth()->user()->role === 'admin')
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('products.edit', $product) }}" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" title="Editar">
                                                <i data-lucide="pencil" class="size-4"></i>
                                            </a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" x-on:click="$dispatch('open-confirm-modal', { title: 'Excluir produto', message: 'Esta ação não pode ser desfeita. Deseja excluir este produto?', confirmText: 'Excluir', tone: 'danger', onConfirm: () => $el.closest('form').submit() })" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-red-50 hover:text-red-600" title="Excluir">
                                                    <i data-lucide="trash-2" class="size-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#e5e0dc] px-4 py-3">
                {{ $products->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

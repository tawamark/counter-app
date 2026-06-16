<x-layouts.app title="Nova contagem">
    <div class="mb-6">
        <a href="{{ route('inventory-counts.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para contagens
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Nova contagem</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Selecione os produtos que serão conferidos nesta contagem.</p>
    </div>

    <form method="POST" action="{{ route('inventory-counts.store') }}" class="max-w-4xl rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="title" class="mb-1 block text-sm font-medium">Título</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ selectAll() { this.$el.querySelectorAll('input[name=\'product_ids[]\']').forEach((checkbox) => checkbox.checked = true); } }">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="block text-sm font-medium">Produtos</label>
                    <div class="flex items-center gap-3">
                        @if ($products->isNotEmpty())
                            <button type="button" x-on:click="selectAll()" class="inline-flex items-center gap-1.5 rounded-md border border-[#e5e0dc] px-2.5 py-1.5 text-xs font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3] hover:text-counter-primary">
                                <i data-lucide="check" class="size-3.5"></i>
                                Selecionar todos
                            </button>
                        @endif
                        <span class="text-xs text-[#6f6f6f]">{{ $products->count() }} disponíveis</span>
                    </div>
                </div>

                @error('product_ids')
                    <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if ($products->isEmpty())
                    <div class="rounded-md border border-[#e5e0dc] bg-[#f7f5f3] px-4 py-6 text-center text-sm text-[#6f6f6f]">
                        Cadastre produtos antes de criar uma contagem.
                    </div>
                @else
                    <div class="max-h-[420px] overflow-y-auto rounded-md border border-[#e5e0dc]">
                        @foreach ($products as $product)
                            <label class="flex cursor-pointer items-center gap-3 border-b border-[#e5e0dc] px-4 py-3 last:border-b-0 hover:bg-orange-50">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(in_array((string) $product->id, old('product_ids', []), true)) class="size-4 rounded border-[#d8d2cc] text-counter-primary focus:ring-counter-primary">
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-medium">{{ $product->name }}</span>
                                    <span class="block text-xs text-[#6f6f6f]">SKU {{ $product->sku }} · saldo {{ number_format((float) $product->current_quantity, 3, ',', '.') }} {{ $product->unit }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('inventory-counts.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
                Cancelar
            </a>
            <button type="submit" @disabled($products->isEmpty()) class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16] disabled:cursor-not-allowed disabled:bg-[#d8d2cc]">
                <i data-lucide="save" class="size-4"></i>
                Criar contagem
            </button>
        </div>
    </form>
</x-layouts.app>

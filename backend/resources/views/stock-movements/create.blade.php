<x-layouts.app title="Nova movimentação">
    <div class="mb-6">
        <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para movimentações
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Nova movimentação</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Registre entrada, saída ou ajuste para atualizar o saldo do produto.</p>
    </div>

    <form method="POST" action="{{ route('stock-movements.store') }}" class="max-w-2xl rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
        @csrf

        <div class="space-y-4">
            <div>
                <label for="product_id" class="mb-1 block text-sm font-medium">Produto</label>
                <select id="product_id" name="product_id" required class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                    <option value="">Selecione um produto</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>{{ $product->name }} - saldo {{ number_format((float) $product->current_quantity, 3, ',', '.') }} {{ $product->unit }}</option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="mb-1 block text-sm font-medium">Tipo</label>
                <select id="type" name="type" required class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                    <option value="entry" @selected(old('type') === 'entry')>Entrada</option>
                    <option value="exit" @selected(old('type') === 'exit')>Saída</option>
                    <option value="adjustment" @selected(old('type') === 'adjustment')>Ajuste</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="mb-1 block text-sm font-medium">Quantidade</label>
                <input id="quantity" name="quantity" type="number" value="{{ old('quantity') }}" required min="0.001" step="0.001" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reason" class="mb-1 block text-sm font-medium">Motivo</label>
                <input id="reason" name="reason" type="text" value="{{ old('reason') }}" maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                <i data-lucide="save" class="size-4"></i>
                Registrar movimentação
            </button>
        </div>
    </form>
</x-layouts.app>

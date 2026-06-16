<form method="POST" action="{{ $action }}" class="max-w-4xl rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name" class="mb-1 block text-sm font-medium">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product?->name) }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sku" class="mb-1 block text-sm font-medium">SKU</label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', $product?->sku) }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('sku')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="barcode" class="mb-1 block text-sm font-medium">Código de barras</label>
            <input id="barcode" name="barcode" type="text" value="{{ old('barcode', $product?->barcode) }}" maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('barcode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="mb-1 block text-sm font-medium">Categoria</label>
            <select id="category_id" name="category_id" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                <option value="">Sem categoria</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="supplier_id" class="mb-1 block text-sm font-medium">Fornecedor</label>
            <select id="supplier_id" name="supplier_id" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                <option value="">Sem fornecedor</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $product?->supplier_id) === (string) $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="unit" class="mb-1 block text-sm font-medium">Unidade</label>
            <input id="unit" name="unit" type="text" value="{{ old('unit', $product?->unit ?? 'un') }}" required maxlength="20" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('unit')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="current_quantity" class="mb-1 block text-sm font-medium">Quantidade atual</label>
            <input id="current_quantity" name="current_quantity" type="number" value="{{ old('current_quantity', $product?->current_quantity ?? '0') }}" required min="0" step="0.001" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('current_quantity')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="cost_price" class="mb-1 block text-sm font-medium">Preço de custo</label>
            <input id="cost_price" name="cost_price" type="number" value="{{ old('cost_price', $product?->cost_price ?? '0') }}" required min="0" step="0.01" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('cost_price')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sale_price" class="mb-1 block text-sm font-medium">Preço de venda</label>
            <input id="sale_price" name="sale_price" type="number" value="{{ old('sale_price', $product?->sale_price ?? '0') }}" required min="0" step="0.01" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('sale_price')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="description" class="mb-1 block text-sm font-medium">Descrição</label>
            <textarea id="description" name="description" rows="4" maxlength="1000" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">{{ old('description', $product?->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
            Cancelar
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="save" class="size-4"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

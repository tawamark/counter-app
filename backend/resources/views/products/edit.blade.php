<x-layouts.app title="Editar produto">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para produtos
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Editar produto</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Atualize as informações do produto selecionado.</p>
    </div>

    @include('products.form', [
        'product' => $product,
        'action' => route('products.update', $product),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
    ])
</x-layouts.app>

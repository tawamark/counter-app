<x-layouts.app title="Novo produto">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para produtos
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Novo produto</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Cadastre um item para controlar saldo, preços e identificação.</p>
    </div>

    @include('products.form', [
        'product' => null,
        'action' => route('products.store'),
        'method' => 'POST',
        'submitLabel' => 'Cadastrar produto',
    ])
</x-layouts.app>

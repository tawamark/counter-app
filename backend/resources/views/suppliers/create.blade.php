<x-layouts.app title="Novo fornecedor">
    <div class="mb-6">
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para fornecedores
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Novo fornecedor</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Cadastre os dados de contato do fornecedor.</p>
    </div>

    @include('suppliers.form', [
        'supplier' => null,
        'action' => route('suppliers.store'),
        'method' => 'POST',
        'submitLabel' => 'Cadastrar fornecedor',
    ])
</x-layouts.app>

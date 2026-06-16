<x-layouts.app title="Editar fornecedor">
    <div class="mb-6">
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para fornecedores
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Editar fornecedor</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Atualize os dados do fornecedor selecionado.</p>
    </div>

    @include('suppliers.form', [
        'supplier' => $supplier,
        'action' => route('suppliers.update', $supplier),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
    ])
</x-layouts.app>

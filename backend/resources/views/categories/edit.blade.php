<x-layouts.app title="Editar categoria">
    <div class="mb-6">
        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para categorias
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Editar categoria</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Atualize as informações da categoria selecionada.</p>
    </div>

    @include('categories.form', [
        'category' => $category,
        'action' => route('categories.update', $category),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
    ])
</x-layouts.app>

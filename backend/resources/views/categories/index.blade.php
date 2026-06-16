<x-layouts.app title="Categorias">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Categorias</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Organize os produtos em grupos para facilitar consulta e controle.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="plus" class="size-4"></i>
            Nova categoria
        </a>
    </div>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($categories->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="tags" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhuma categoria cadastrada</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Cadastre categorias para separar produtos por tipo, setor ou finalidade.</p>
                <a href="{{ route('categories.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                    <i data-lucide="plus" class="size-4"></i>
                    Cadastrar categoria
                </a>
            </div>
        @else
            <div class="overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Nome</th>
                            <th class="px-4 py-3 font-semibold">Descrição</th>
                            <th class="px-4 py-3 font-semibold">Produtos</th>
                            <th class="px-4 py-3 text-right font-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $category->description ?: 'Sem descrição' }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $category->products_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('categories.edit', $category) }}" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" title="Editar">
                                            <i data-lucide="pencil" class="size-4"></i>
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" x-on:click="$dispatch('open-confirm-modal', { title: 'Excluir categoria', message: 'Esta ação não pode ser desfeita. Deseja excluir esta categoria?', confirmText: 'Excluir', tone: 'danger', onConfirm: () => $el.closest('form').submit() })" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-red-50 hover:text-red-600" title="Excluir">
                                                <i data-lucide="trash-2" class="size-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#e5e0dc] px-4 py-3">
                {{ $categories->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

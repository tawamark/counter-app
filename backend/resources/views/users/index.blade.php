<x-layouts.app title="Usuários">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Usuários</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Gerencie os acessos da empresa por perfil de permissão.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="plus" class="size-4"></i>
            Novo usuário
        </a>
    </div>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($users->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="users" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhum usuário cadastrado</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Cadastre usuários para separar acessos administrativos, estoque e contagem.</p>
                <a href="{{ route('users.create') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                    <i data-lucide="plus" class="size-4"></i>
                    Cadastrar usuário
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Nome</th>
                            <th class="px-4 py-3 font-semibold">E-mail</th>
                            <th class="px-4 py-3 font-semibold">Perfil</th>
                            <th class="px-4 py-3 font-semibold">Criado em</th>
                            <th class="px-4 py-3 text-right font-semibold">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ ['admin' => 'Administrador', 'stockist' => 'Estoquista', 'counter' => 'Contador'][$user->role] ?? $user->role }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" title="Editar">
                                            <i data-lucide="pencil" class="size-4"></i>
                                        </a>
                                        <form method="POST" action="{{ route('users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" x-on:click="$dispatch('open-confirm-modal', { title: 'Excluir usuário', message: 'Esta ação não pode ser desfeita. Deseja excluir este usuário?', confirmText: 'Excluir', tone: 'danger', onConfirm: () => $el.closest('form').submit() })" @disabled($user->id === auth()->id()) class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:bg-[#f7f5f3] disabled:text-[#c7c0ba]" title="Excluir">
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
                {{ $users->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

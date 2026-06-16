<x-layouts.app title="Editar usuário">
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-counter-primary">
            <i data-lucide="chevron-down" class="size-4 rotate-90"></i>
            Voltar para usuários
        </a>
        <h1 class="mt-3 text-2xl font-semibold">Editar usuário</h1>
        <p class="mt-1 text-sm text-[#6f6f6f]">Atualize dados, perfil ou senha do usuário.</p>
    </div>

    @include('users.form', [
        'user' => $user,
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'submitLabel' => 'Salvar usuário',
    ])
</x-layouts.app>

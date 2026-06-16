@php
    $isEditingAuthenticatedUser = $user?->id === auth()->id();
@endphp

<form method="POST" action="{{ $action }}" class="max-w-2xl rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name" class="mb-1 block text-sm font-medium">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user?->name) }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            @if ($isEditingAuthenticatedUser)
                <label for="role_display" class="mb-1 block text-sm font-medium">Perfil</label>
                <input id="role_display" type="text" value="{{ ['admin' => 'Administrador', 'stockist' => 'Estoquista', 'counter' => 'Contador'][$user->role] ?? $user->role }}" disabled class="block w-full rounded-md border border-[#d8d2cc] bg-[#f7f5f3] px-3 py-2 text-sm text-[#6f6f6f] outline-none">
                <input type="hidden" name="role" value="{{ $user->role }}">
                <p class="mt-1 text-xs text-[#6f6f6f]">O próprio perfil de acesso não pode ser alterado.</p>
            @else
                <x-dropdown-select name="role" label="Perfil" :selected="old('role', $user?->role ?? 'admin')" :options="['admin' => 'Administrador', 'stockist' => 'Estoquista', 'counter' => 'Contador']" />
            @endif
            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="password" class="mb-1 block text-sm font-medium">Senha</label>
            <input id="password" name="password" type="password" @required($method === 'POST') minlength="8" maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @if ($method !== 'POST')
                <p class="mt-1 text-xs text-[#6f6f6f]">Deixe em branco para manter a senha atual.</p>
            @endif
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
            Cancelar
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="save" class="size-4"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

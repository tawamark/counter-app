<form method="POST" action="{{ $action }}" class="max-w-2xl rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name" class="mb-1 block text-sm font-medium">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $supplier?->name) }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="cnpj" class="mb-1 block text-sm font-medium">CNPJ</label>
            <input id="cnpj" name="cnpj" type="text" value="{{ old('cnpj', $supplier?->cnpj) }}" maxlength="20" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('cnpj')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="mb-1 block text-sm font-medium">Telefone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $supplier?->phone) }}" maxlength="30" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $supplier?->email) }}" maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="address" class="mb-1 block text-sm font-medium">Endereço</label>
            <input id="address" name="address" type="text" value="{{ old('address', $supplier?->address) }}" maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
            Cancelar
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="save" class="size-4"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

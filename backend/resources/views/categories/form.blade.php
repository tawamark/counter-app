<form method="POST" action="{{ $action }}" class="max-w-2xl rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-4">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $category?->name) }}" required maxlength="255" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Descrição</label>
            <textarea id="description" name="description" rows="4" maxlength="1000" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">{{ old('description', $category?->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('categories.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
            Cancelar
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
            <i data-lucide="save" class="size-4"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>

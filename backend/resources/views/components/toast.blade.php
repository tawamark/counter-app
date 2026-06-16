@props([
    'message',
    'type' => 'success',
])

@php
    $settings = [
        'success' => [
            'title' => 'Tudo certo',
            'icon' => 'circle-check',
            'iconClass' => 'text-green-600',
            'barClass' => 'bg-green-600',
        ],
        'error' => [
            'title' => 'Erro',
            'icon' => 'circle-x',
            'iconClass' => 'text-red-600',
            'barClass' => 'bg-red-600',
        ],
        'info' => [
            'title' => 'Informação',
            'icon' => 'info',
            'iconClass' => 'text-blue-600',
            'barClass' => 'bg-blue-600',
        ],
        'warning' => [
            'title' => 'Aviso',
            'icon' => 'alert-triangle',
            'iconClass' => 'text-yellow-600',
            'barClass' => 'bg-yellow-500',
        ],
    ][$type] ?? [
        'title' => 'Informação',
        'icon' => 'info',
        'iconClass' => 'text-blue-600',
        'barClass' => 'bg-blue-600',
    ];
@endphp

<div x-data="{ show: true, progress: 100, timer: null, progressTimer: null, close() { this.show = false; clearTimeout(this.timer); clearInterval(this.progressTimer); } }" x-init="timer = setTimeout(() => close(), 5000); progressTimer = setInterval(() => progress = Math.max(progress - 2, 0), 100)" x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-2 opacity-0" class="fixed right-4 top-4 z-50 w-[calc(100vw-2rem)] max-w-sm overflow-hidden rounded-lg border border-[#d8d2cc] bg-counter-bg shadow-md" role="status" aria-live="polite">
    <div class="flex gap-3 p-4">
        <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-md bg-[#f7f5f3] {{ $settings['iconClass'] }}">
            <i data-lucide="{{ $settings['icon'] }}" class="size-4"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold">{{ $settings['title'] }}</p>
            <p class="mt-1 text-sm text-[#6f6f6f]">{{ $message }}</p>
        </div>
        <button type="button" x-on:click="close()" class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-[#6f6f6f] transition hover:bg-[#f7f5f3] hover:text-counter-primary" aria-label="Fechar aviso">
            <i data-lucide="x" class="size-4"></i>
        </button>
    </div>

    <div class="h-1 bg-[#f0ebe7]">
        <div class="h-full {{ $settings['barClass'] }} transition-[width] duration-100 linear" x-bind:style="`width: ${progress}%`"></div>
    </div>
</div>

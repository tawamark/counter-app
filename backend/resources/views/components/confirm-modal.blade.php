<div x-data="{ open: false, title: '', message: '', confirmText: 'Confirmar', cancelText: 'Cancelar', tone: 'danger', onConfirm: null, close() { this.open = false; this.onConfirm = null; }, confirm() { if (typeof this.onConfirm === 'function') { this.onConfirm(); } this.close(); } }" x-on:open-confirm-modal.window="open = true; title = $event.detail.title || 'Confirmar ação'; message = $event.detail.message || 'Deseja continuar?'; confirmText = $event.detail.confirmText || 'Confirmar'; cancelText = $event.detail.cancelText || 'Cancelar'; tone = $event.detail.tone || 'danger'; onConfirm = $event.detail.onConfirm || null; $nextTick(() => $refs.cancelButton?.focus())" x-on:keydown.escape.window="if (open) close()" x-cloak x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-[#323232]/45" x-on:click="close()"></div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-2 scale-95 opacity-0" x-transition:enter-end="translate-y-0 scale-100 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 scale-100 opacity-100" x-transition:leave-end="translate-y-2 scale-95 opacity-0" class="relative w-full max-w-md overflow-hidden rounded-lg border border-[#d8d2cc] bg-counter-bg shadow-xl">
        <div class="flex gap-3 p-5">
            <div class="mt-0.5 flex size-10 shrink-0 items-center justify-center rounded-md bg-[#f7f5f3]" x-bind:class="tone === 'danger' ? 'text-red-600' : 'text-counter-primary'">
                <i data-lucide="alert-triangle" x-show="tone === 'danger'" class="size-5"></i>
                <i data-lucide="circle-check" x-show="tone !== 'danger'" class="size-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-base font-semibold" x-text="title"></h2>
                <p class="mt-2 text-sm leading-6 text-[#6f6f6f]" x-text="message"></p>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-[#e5e0dc] bg-[#fbfaf9] px-5 py-4 sm:flex-row sm:justify-end">
            <button type="button" x-ref="cancelButton" x-on:click="close()" class="inline-flex items-center justify-center rounded-md border border-[#d8d2cc] bg-counter-bg px-4 py-2.5 text-sm font-semibold text-[#323232] transition hover:bg-[#f7f5f3]" x-text="cancelText"></button>
            <button type="button" x-on:click="confirm()" class="inline-flex items-center justify-center rounded-md px-4 py-2.5 text-sm font-semibold text-white transition" x-bind:class="tone === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-counter-primary hover:bg-[#e85f16]'" x-text="confirmText"></button>
        </div>
    </div>
</div>

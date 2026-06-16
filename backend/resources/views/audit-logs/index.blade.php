<x-layouts.app title="Auditoria">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Auditoria</h1>
            <p class="mt-1 text-sm text-[#6f6f6f]">Acompanhe ações administrativas e operações importantes do estoque.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-4 rounded-lg border border-[#e5e0dc] bg-counter-bg p-4 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-5">
            <x-dropdown-select name="module" label="Módulo" :selected="$filters['module'] ?? ''" :options="['' => 'Todos', 'categorias' => 'Categorias', 'fornecedores' => 'Fornecedores', 'produtos' => 'Produtos', 'usuarios' => 'Usuários', 'movimentacoes' => 'Movimentações', 'contagens' => 'Contagens']" />

            <x-dropdown-select name="action" label="Ação" :selected="$filters['action'] ?? ''" :options="['' => 'Todas', 'criou' => 'Criou', 'atualizou' => 'Atualizou', 'excluiu' => 'Excluiu', 'registrou' => 'Registrou', 'finalizou' => 'Finalizou', 'aprovou' => 'Aprovou', 'alterou' => 'Alterou']" />

            <x-dropdown-select name="user_id" label="Usuário" :selected="$filters['user_id'] ?? ''" :options="collect(['' => 'Todos'])->union($users->pluck('name', 'id'))->all()" />

            <div>
                <label for="date_from" class="mb-1 block text-sm font-medium">De</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            </div>

            <div>
                <label for="date_to" class="mb-1 block text-sm font-medium">Até</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
            </div>
        </div>

        <div class="mt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center justify-center rounded-md border border-[#e5e0dc] px-4 py-2.5 text-sm font-semibold text-[#6f6f6f] transition hover:bg-[#f7f5f3]">
                Limpar
            </a>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16]">
                <i data-lucide="search" class="size-4"></i>
                Filtrar
            </button>
        </div>
    </form>

    <section class="rounded-lg border border-[#e5e0dc] bg-counter-bg shadow-sm">
        @if ($logs->isEmpty())
            <div class="flex min-h-60 flex-col items-center justify-center px-6 py-10 text-center">
                <i data-lucide="history" class="size-10 text-counter-primary"></i>
                <h2 class="mt-4 text-lg font-semibold">Nenhum registro encontrado</h2>
                <p class="mt-1 max-w-sm text-sm text-[#6f6f6f]">Execute ações no sistema ou ajuste os filtros para visualizar o histórico.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[960px] text-left text-sm">
                    <thead class="bg-[#f7f5f3] text-xs uppercase text-[#6f6f6f]">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Data</th>
                            <th class="px-4 py-3 font-semibold">Usuário</th>
                            <th class="px-4 py-3 font-semibold">Módulo</th>
                            <th class="px-4 py-3 font-semibold">Ação</th>
                            <th class="px-4 py-3 font-semibold">Descrição</th>
                            <th class="px-4 py-3 font-semibold">Origem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e0dc]">
                        @foreach ($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $log->user?->name ?? 'Usuário removido' }}</div>
                                    <div class="text-xs text-[#6f6f6f]">{{ $log->user?->email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ ['categorias' => 'Categorias', 'fornecedores' => 'Fornecedores', 'produtos' => 'Produtos', 'usuarios' => 'Usuários', 'movimentacoes' => 'Movimentações', 'contagens' => 'Contagens'][$log->module] ?? $log->module }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-counter-primary">
                                        {{ ['criou' => 'Criou', 'atualizou' => 'Atualizou', 'excluiu' => 'Excluiu', 'registrou' => 'Registrou', 'finalizou' => 'Finalizou', 'aprovou' => 'Aprovou', 'alterou' => 'Alterou'][$log->action] ?? $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-[#6f6f6f]">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-[#e5e0dc] px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>

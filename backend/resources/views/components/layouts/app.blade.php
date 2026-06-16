<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Counter') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7f5f3] text-counter-text antialiased">
        @php
            $toastMessage = session('status') ?? session('error') ?? session('info') ?? session('warning') ?? $errors->first('count') ?? $errors->first('items');
            $toastType = match (true) {
                session()->has('error') || $errors->has('count') || $errors->has('items') => 'error',
                session()->has('warning') => 'warning',
                session()->has('info') => 'info',
                default => 'success',
            };
        @endphp

        @if ($toastMessage)
            <x-toast :message="$toastMessage" :type="$toastType" />
        @endif

        <x-app-loader />

        <x-confirm-modal />

        <div x-data="{ mobileMenuOpen: false }" x-on:keydown.escape.window="mobileMenuOpen = false" class="min-h-screen">
            <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-[#e5e0dc] bg-counter-bg lg:flex">
                <div class="flex h-16 shrink-0 items-center border-b border-[#e5e0dc] px-6">
                    <img src="{{ asset('images/logo.svg') }}" alt="Counter" class="h-10 w-auto">
                </div>

                <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                        <i data-lucide="layout-dashboard" class="size-4"></i>
                        Dashboard
                    </a>

                    @if (in_array(auth()->user()->role, ['admin', 'stockist'], true))
                        <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="package" class="size-4"></i>
                            Produtos
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="tags" class="size-4"></i>
                            Categorias
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="truck" class="size-4"></i>
                            Fornecedores
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="users" class="size-4"></i>
                            Usuários
                        </a>
                        <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('audit-logs.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="history" class="size-4"></i>
                            Auditoria
                        </a>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'stockist'], true))
                        <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('stock-movements.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="arrow-down-up" class="size-4"></i>
                            Movimentações
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="file-down" class="size-4"></i>
                            Relatórios
                        </a>
                    @endif

                    @if (in_array(auth()->user()->role, ['admin', 'counter'], true))
                        <a href="{{ route('inventory-counts.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('inventory-counts.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="clipboard-list" class="size-4"></i>
                            Contagens
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('divergences.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('divergences.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="scale" class="size-4"></i>
                            Divergências
                        </a>
                    @endif
                </nav>
            </aside>

            <div x-cloak x-show="mobileMenuOpen" class="fixed inset-0 z-40 lg:hidden">
                <button type="button" x-on:click="mobileMenuOpen = false" class="absolute inset-0 bg-[#323232]/40" aria-label="Fechar menu"></button>
                <aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex h-full w-72 max-w-[85vw] flex-col border-r border-[#e5e0dc] bg-counter-bg shadow-xl">
                    <div class="flex h-16 items-center justify-between border-b border-[#e5e0dc] px-4">
                        <img src="{{ asset('images/logo.svg') }}" alt="Counter" class="h-10 w-auto">
                        <button type="button" x-on:click="mobileMenuOpen = false" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" aria-label="Fechar menu">
                            <i data-lucide="x" class="size-4"></i>
                        </button>
                    </div>

                    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                        <a href="{{ route('dashboard') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                            <i data-lucide="layout-dashboard" class="size-4"></i>
                            Dashboard
                        </a>

                        @if (in_array(auth()->user()->role, ['admin', 'stockist'], true))
                            <a href="{{ route('products.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('products.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="package" class="size-4"></i>
                                Produtos
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('categories.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="tags" class="size-4"></i>
                                Categorias
                            </a>
                            <a href="{{ route('suppliers.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="truck" class="size-4"></i>
                                Fornecedores
                            </a>
                            <a href="{{ route('users.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="users" class="size-4"></i>
                                Usuários
                            </a>
                            <a href="{{ route('audit-logs.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('audit-logs.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="history" class="size-4"></i>
                                Auditoria
                            </a>
                        @endif

                        @if (in_array(auth()->user()->role, ['admin', 'stockist'], true))
                            <a href="{{ route('stock-movements.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('stock-movements.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="arrow-down-up" class="size-4"></i>
                                Movimentações
                            </a>
                            <a href="{{ route('reports.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="file-down" class="size-4"></i>
                                Relatórios
                            </a>
                        @endif

                        @if (in_array(auth()->user()->role, ['admin', 'counter'], true))
                            <a href="{{ route('inventory-counts.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('inventory-counts.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="clipboard-list" class="size-4"></i>
                                Contagens
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('divergences.index') }}" x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('divergences.*') ? 'bg-orange-50 text-counter-primary' : 'text-[#6f6f6f] hover:bg-orange-50 hover:text-counter-primary' }}">
                                <i data-lucide="scale" class="size-4"></i>
                                Divergências
                            </a>
                        @endif
                    </nav>
                </aside>
            </div>

            <div class="min-w-0 lg:pl-64">
                <header class="fixed left-0 right-0 top-0 z-30 flex h-16 items-center justify-between border-b border-[#e5e0dc] bg-counter-bg px-4 sm:px-6 lg:left-64">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" x-on:click="mobileMenuOpen = true" class="inline-flex size-9 shrink-0 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary lg:hidden" aria-label="Abrir menu">
                            <i data-lucide="menu" class="size-4"></i>
                        </button>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ $title ?? 'Counter' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-md border border-[#e5e0dc] bg-counter-bg px-2 py-1.5 shadow-sm">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#f7f5f3] text-sm font-semibold text-[#323232]">
                            {{ Str::of(auth()->user()->name)->trim()->substr(0, 1)->upper() }}
                        </div>
                        <div class="hidden text-left sm:block">
                            <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#6f6f6f]">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex size-9 items-center justify-center rounded-md border border-[#e5e0dc] text-[#6f6f6f] transition hover:bg-orange-50 hover:text-counter-primary" title="Sair">
                                <i data-lucide="log-out" class="size-4"></i>
                            </button>
                        </form>
                    </div>
                </header>

                <main class="px-4 pb-4 pt-20 sm:px-6 sm:pb-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

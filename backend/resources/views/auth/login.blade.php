<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Entrar | {{ config('app.name', 'Counter') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-counter-bg text-counter-text antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
            <div class="w-full max-w-sm">
                <div class="mb-8 flex justify-center">
                    <img src="{{ asset('images/logo.svg') }}" alt="Counter" class="h-16 w-auto">
                </div>

                <div class="rounded-lg border border-[#e5e0dc] bg-counter-bg p-6 shadow-sm">
                    <div class="mb-6">
                        <h1 class="text-xl font-semibold">Entrar</h1>
                        <p class="mt-1 text-sm text-[#6f6f6f]">Informe suas credenciais para acessar o sistema.</p>

                        @if ($errors->any())
                            <div class="mt-4 space-y-2 rounded-md border border-[#d8d2cc] bg-white px-3 py-3">
                                @foreach (array_unique($errors->all()) as $message)
                                    <div class="flex items-start gap-2 text-sm text-red-600">
                                        <i data-lucide="circle-x" class="mt-0.5 size-4 shrink-0"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" x-data="{ submitting: false }" x-on:submit="submitting = true" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium">E-mail</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="exemplo@empresa.com" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                        </div>

                        <div x-data="{ showPassword: false }">
                            <label for="password" class="mb-1 block text-sm font-medium">Senha</label>
                            <div class="relative">
                                <input id="password" name="password" x-bind:type="showPassword ? 'text' : 'password'" required autocomplete="current-password" class="block w-full rounded-md border border-[#d8d2cc] px-3 py-2 pr-11 text-sm outline-none transition focus:border-counter-primary focus:ring-2 focus:ring-orange-100">
                                <button type="button" x-on:click="showPassword = ! showPassword" x-bind:aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'" class="absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center text-[#6f6f6f] transition hover:text-counter-primary">
                                    <i x-show="! showPassword" data-lucide="eye" class="size-4"></i>
                                    <i x-show="showPassword" data-lucide="eye-off" class="size-4"></i>
                                </button>
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-[#6f6f6f]">
                            <input name="remember" type="checkbox" value="1" class="size-4 rounded border-[#d8d2cc] text-counter-primary focus:ring-counter-primary">
                            Manter conectado
                        </label>

                        <button type="submit" x-bind:disabled="submitting" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-counter-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85f16] disabled:cursor-not-allowed disabled:opacity-80">
                            <span x-show="submitting" class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            <span x-text="submitting ? 'Entrando' : 'Entrar'">Entrar</span>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>

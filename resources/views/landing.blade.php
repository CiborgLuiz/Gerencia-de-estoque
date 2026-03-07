<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gerência de Estoque</title>
        <script>
            (() => {
                const theme = localStorage.getItem('theme') ?? 'dark';
                document.documentElement.classList.toggle('dark', theme === 'dark');
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <main class="min-h-screen flex items-center justify-center p-6">
            <section class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-8 sm:p-10 dark:bg-gray-900 dark:border dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">Sistema de Vendas e Estoque</p>
                    <button
                        type="button"
                        onclick="window.setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    >
                        Alternar tema
                    </button>
                </div>
                <h1 class="mt-3 text-3xl sm:text-4xl font-bold">Bem-vindo ao painel de gerência</h1>
                <p class="mt-4 text-gray-600 leading-relaxed dark:text-gray-300">
                    Acompanhe produtos, registre vendas e visualize indicadores em um só lugar.
                    Faça login para acessar o painel administrativo.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-100 dark:hover:bg-gray-800">
                        Criar conta
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>

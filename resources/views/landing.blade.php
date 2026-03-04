<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gerência de Estoque</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100 text-gray-900">
        <main class="min-h-screen flex items-center justify-center p-6">
            <section class="max-w-2xl w-full bg-white rounded-2xl shadow-lg p-8 sm:p-10">
                <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">Sistema de Vendas e Estoque</p>
                <h1 class="mt-3 text-3xl sm:text-4xl font-bold">Bem-vindo ao painel de gerência</h1>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Acompanhe produtos, registre vendas e visualize indicadores em um só lugar.
                    Faça login para acessar o painel administrativo.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
                        Criar conta
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>

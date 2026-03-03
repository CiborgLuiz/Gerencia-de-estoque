<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Painel administrativo</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.products.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md">
                <h3 class="font-semibold text-lg">Gerenciar produtos</h3>
                <p class="text-sm text-gray-500 mt-1">Acesse cadastro e movimentações rápidas.</p>
            </a>

            <a href="{{ route('admin.movements.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-md">
                <h3 class="font-semibold text-lg">Histórico de movimentações</h3>
                <p class="text-sm text-gray-500 mt-1">Consulte entradas e saídas recentes.</p>
            </a>
        </div>
    </div>
</x-app-layout>

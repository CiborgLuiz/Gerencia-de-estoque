<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Painel Administrativo</h1>

        <div class="grid grid-cols-3 gap-6">

            <a href="{{ route('admin.products.index') }}"
               class="bg-white shadow p-6 rounded hover:shadow-lg">
                📦 Gerenciar Produtos
            </a>

            <a href="#"
               class="bg-white shadow p-6 rounded hover:shadow-lg">
                📊 Relatórios
            </a>

            <a href="#"
               class="bg-white shadow p-6 rounded hover:shadow-lg">
                👥 Usuários
            </a>

        </div>
    </div>
</x-app-layout>
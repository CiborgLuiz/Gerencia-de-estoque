<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Painel administrativo</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('products.manage') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Gerenciar produtos</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Acesse cadastro e movimentações rápidas.</p>
            </a>

            <a href="{{ route('admin.movements.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Histórico de movimentações</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Consulte entradas e saídas recentes.</p>
            </a>

            <a href="{{ route('invoices.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Acessar NF-e</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Visualize e abra notas fiscais emitidas.</p>
            </a>

            <a href="{{ route('nfse.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Acessar NFS-e</h3>
                <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Cadastre servicos, emita NFS-e e consulte o XML.</p>
            </a>

            @if(auth()->user()?->hasRole('dono', 'admin'))
                <a href="{{ route('admin.access-keys.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Gerar chaves de colaboradores</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Crie novas chaves de acesso para a equipe.</p>
                </a>
            @endif

            @if(auth()->user()?->hasRole('dono'))
                <a href="{{ route('admin.employees.index') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Gerenciar funcionários</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">Visualize contas vinculadas e desvincule colaboradores.</p>
                </a>
            @endif
        </div>
    </div>
</x-app-layout>

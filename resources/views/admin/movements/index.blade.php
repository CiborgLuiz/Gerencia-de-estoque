<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Histórico de movimentações</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 rounded-lg border border-gray-200 bg-white shadow overflow-hidden dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                <thead class="bg-gray-50 text-left dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3">Produto</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Quantidade</th>
                        <th class="px-4 py-3">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-3">{{ $movement->product->name ?? 'Produto removido' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($movement->type) }}</td>
                            <td class="px-4 py-3">{{ $movement->quantity }}</td>
                            <td class="px-4 py-3">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-300">Sem movimentações registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Histórico de movimentações</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3">Produto</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Quantidade</th>
                        <th class="px-4 py-3">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $movement->product->name ?? 'Produto removido' }}</td>
                            <td class="px-4 py-3">{{ ucfirst($movement->type) }}</td>
                            <td class="px-4 py-3">{{ $movement->quantity }}</td>
                            <td class="px-4 py-3">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Sem movimentações registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4 border-t">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

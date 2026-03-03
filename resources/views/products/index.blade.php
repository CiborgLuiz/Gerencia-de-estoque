<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produtos</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-4 rounded-lg shadow">
                <form method="GET" class="flex flex-col sm:flex-row items-end gap-3">
                    <div>
                        <label for="category_id" class="block text-sm text-gray-600">Filtrar por categoria</label>
                        <select id="category_id" name="category_id" class="border-gray-300 rounded-md shadow-sm">
                            <option value="">Todas as categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="px-4 py-2 bg-gray-800 text-white rounded-md">Aplicar</button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3">Nome</th>
                            <th class="px-4 py-3">Categoria</th>
                            <th class="px-4 py-3">Preço</th>
                            <th class="px-4 py-3">Estoque</th>
                            <th class="px-4 py-3">Movimentação rápida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $product->name }}</td>
                                <td class="px-4 py-3">{{ $product->category?->name ?? '-' }}</td>
                                <td class="px-4 py-3">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 {{ $product->stock <= 5 ? 'text-red-600 font-semibold' : '' }}">{{ $product->stock }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.movements.store') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="type" value="entrada">
                                            <input type="hidden" name="quantity" value="1">
                                            <button class="px-3 py-1 bg-green-600 text-white rounded">+1</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.movements.store') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="type" value="saida">
                                            <input type="hidden" name="quantity" value="1">
                                            <button class="px-3 py-1 bg-red-600 text-white rounded">-1</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">Nenhum produto encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

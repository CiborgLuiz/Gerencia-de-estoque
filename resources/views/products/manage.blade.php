<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Gestão de Produtos</h2></x-slot>
    <div class="p-6 space-y-6">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded shadow">
            @csrf
            <input name="name" placeholder="Nome" class="rounded border" required>
            <input name="internal_code" placeholder="Código interno" class="rounded border" required>
            <input name="ncm" placeholder="NCM" class="rounded border" required>
            <input name="purchase_price" type="number" step="0.01" placeholder="Preço compra" class="rounded border" required>
            <input name="sale_price" type="number" step="0.01" placeholder="Preço venda" class="rounded border" required>
            <input name="stock" type="number" min="0" placeholder="Estoque" class="rounded border" required>
            <input name="minimum_stock" type="number" min="0" placeholder="Estoque mínimo" class="rounded border" required>
            <select name="status" class="rounded border" required><option value="ativo">Ativo</option><option value="inativo">Inativo</option></select>
            <select name="category_id" class="rounded border" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="file" name="image" class="rounded border">
            <button class="bg-indigo-600 text-white px-3 py-2 rounded">Salvar</button>
        </form>

        <div class="bg-white rounded shadow overflow-auto">
            <table class="min-w-full text-sm">
                <thead><tr><th class="p-2">Produto</th><th>Categoria</th><th>Estoque</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($products as $product)
                        <tr class="border-t">
                            <td class="p-2">{{ $product->name }} <small class="text-gray-500">{{ $product->internal_code }}</small></td>
                            <td>{{ $product->category?->name }}</td>
                            <td @class(['text-red-600'=>$product->stock <= $product->minimum_stock])>{{ $product->stock }}</td>
                            <td>{{ $product->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Gestão de Produtos</h2></x-slot>
    <div class="p-6 space-y-6">
        @if ($errors->any())
            <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200">
                <p class="font-semibold mb-2">Não foi possível salvar. Verifique os campos:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <form method="POST" action="{{ route('categories.store') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 space-y-3">
                @csrf
                <h3 class="font-semibold">Criar categoria</h3>
                <input name="name" value="{{ old('name') }}" placeholder="Nome da categoria" class="w-full rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
                <textarea name="description" placeholder="Descrição (opcional)" rows="2" class="w-full rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">{{ old('description') }}</textarea>
                <select name="parent_id" class="w-full rounded border border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">Sem categoria (raiz)</option>
                    @foreach($flatCategories as $category)
                        <option value="{{ $category['id'] }}" @selected((string) old('parent_id') === (string) $category['id'])>{{ $category['name'] }}</option>
                    @endforeach
                </select>
                <button class="bg-indigo-600 text-white px-3 py-2 rounded">Salvar categoria</button>
            </form>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <h3 class="font-semibold mb-3">Estrutura de categorias</h3>
                @if (empty($categoryTree))
                    <p class="text-sm text-gray-600 dark:text-gray-300">Nenhuma categoria cadastrada ainda.</p>
                @else
                    <ul class="space-y-1">
                        @foreach($categoryTree as $node)
                            @include('products.partials.category-tree-node', ['node' => $node, 'depth' => 0])
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            @csrf
            <input name="name" value="{{ old('name') }}" placeholder="Nome" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <input name="internal_code" value="{{ old('internal_code') }}" placeholder="Código interno" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <input name="ncm" value="{{ old('ncm') }}" placeholder="NCM" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <input name="purchase_price" value="{{ old('purchase_price') }}" type="number" step="0.01" placeholder="Preço compra" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <input name="sale_price" value="{{ old('sale_price') }}" type="number" step="0.01" placeholder="Preço venda" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <input name="price" value="{{ old('price', old('sale_price')) }}" type="hidden">
            <input name="stock" value="{{ old('stock') }}" type="number" min="0" placeholder="Estoque" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <input name="minimum_stock" value="{{ old('minimum_stock') }}" type="number" min="0" placeholder="Estoque mínimo" class="rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900" required>
            <select name="status" class="rounded border border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                <option value="ativo" @selected(old('status', 'ativo') === 'ativo')>Ativo</option>
                <option value="inativo" @selected(old('status') === 'inativo')>Inativo</option>
            </select>
            <select name="category_id" class="rounded border border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                @if(count($flatCategories) === 0)
                    <option value="">Cadastre uma categoria primeiro</option>
                @else
                    @foreach($flatCategories as $category)
                        <option value="{{ $category['id'] }}" @selected((string) old('category_id') === (string) $category['id'])>{{ $category['name'] }}</option>
                    @endforeach
                @endif
            </select>
            <div class="space-y-1">
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif" class="w-full rounded border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-900">
                @error('image')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button class="bg-indigo-600 text-white px-3 py-2 rounded" @disabled(count($flatCategories) === 0)>Salvar</button>
        </form>

        <div class="rounded-lg border border-gray-200 bg-white shadow overflow-auto dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                <thead>
                    <tr>
                        <th class="p-3 text-left">Imagem</th>
                        <th class="p-3 text-left">Produto</th>
                        <th class="p-3 text-left">Categoria</th>
                        <th class="p-3 text-left">Estoque</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @php($currentCategory = null)
                    @foreach($products as $product)
                        @if($currentCategory !== ($product->category?->name ?? 'Sem categoria'))
                            @php($currentCategory = $product->category?->name ?? 'Sem categoria')
                            <tr class="border-t border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/60">
                                <td colspan="6" class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                    Categoria: {{ $currentCategory }}
                                </td>
                            </tr>
                        @endif
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-3 align-middle">
                                @if($product->image_path)
                                    <img src="{{ route('products.image', $product) }}" alt="Imagem de {{ $product->name }}" class="h-12 w-12 rounded object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded bg-gray-200 text-[10px] font-semibold uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                        Sem
                                    </div>
                                @endif
                            </td>
                            <td class="p-3 align-middle">
                                <a href="{{ route('admin.products.show', $product) }}" class="font-medium text-indigo-600 hover:underline dark:text-indigo-400">
                                    {{ $product->name }}
                                </a>
                                <small class="ml-1 text-gray-500 dark:text-gray-400">{{ $product->internal_code }}</small>
                            </td>
                            <td class="p-3 align-middle">{{ $product->category?->name }}</td>
                            <td @class(['p-3 align-middle', 'text-red-600' => $product->stock <= $product->minimum_stock])>{{ $product->stock }}</td>
                            <td class="p-3 align-middle">{{ $product->status }}</td>
                            <td class="p-3 align-middle">
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('admin.products.show', $product) }}" class="text-sky-600 hover:underline dark:text-sky-400">Abrir</a>
                                    <a href="{{ route('sales.product', $product) }}" class="text-indigo-600 hover:underline dark:text-indigo-400">Vender este item</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirmDeleteProduct(event, '{{ $product->internal_code }}', '{{ addslashes($product->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="confirmation" value="">
                                        <button type="submit" class="text-red-600 hover:underline dark:text-red-400">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>

    <script>
        function confirmDeleteProduct(event, internalCode, productName) {
            const form = event.target;
            const typed = window.prompt(`Para apagar "${productName}", digite o código interno (${internalCode}).`);

            if (typed === null) {
                event.preventDefault();
                return false;
            }

            if (typed.trim() !== internalCode) {
                event.preventDefault();
                window.alert('Código interno incorreto. Produto não apagado.');
                return false;
            }

            form.querySelector('input[name="confirmation"]').value = typed.trim();
            return true;
        }
    </script>
</x-app-layout>

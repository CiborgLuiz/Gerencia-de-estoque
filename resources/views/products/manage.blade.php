<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Gestão de Produtos</h2></x-slot>
    <div class="p-6">
        <div class="mx-auto max-w-7xl space-y-6">
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

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    @csrf
                    <div>
                        <h3 class="font-semibold text-lg">Criar categoria</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Organize o catálogo antes de cadastrar produtos.</p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Nome da categoria</label>
                            <input name="name" value="{{ old('name') }}" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Descrição</label>
                            <textarea name="description" rows="2" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Categoria pai</label>
                            <select name="parent_id" class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Sem categoria (raiz)</option>
                                @foreach($flatCategories as $category)
                                    <option value="{{ $category['id'] }}" @selected((string) old('parent_id') === (string) $category['id'])>{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Salvar categoria</button>
                </form>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <h3 class="font-semibold text-lg mb-3">Estrutura de categorias</h3>
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

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                @csrf
                <div>
                    <h3 class="font-semibold text-lg">Cadastrar produto</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Preencha os dados principais, preços, estoque e imagem do item.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Nome</label>
                        <input name="name" value="{{ old('name') }}" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Código interno</label>
                        <input name="internal_code" value="{{ old('internal_code') }}" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Código fabricante</label>
                        <input name="manufacturer_code" value="{{ old('manufacturer_code') }}" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">NCM</label>
                        <input name="ncm" value="{{ old('ncm') }}" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Preço de compra</label>
                        <input name="purchase_price" value="{{ old('purchase_price') }}" type="number" step="0.01" min="0" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Preço de venda</label>
                        <input name="sale_price" value="{{ old('sale_price') }}" type="number" step="0.01" min="0" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <input name="price" value="{{ old('price', old('sale_price')) }}" type="hidden">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Estoque</label>
                        <input name="stock" value="{{ old('stock') }}" type="number" min="0" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Estoque mínimo</label>
                        <input name="minimum_stock" value="{{ old('minimum_stock') }}" type="number" min="0" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Status</label>
                        <select name="status" class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            <option value="ativo" @selected(old('status', 'ativo') === 'ativo')>Ativo</option>
                            <option value="inativo" @selected(old('status') === 'inativo')>Inativo</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Categoria</label>
                        <select name="category_id" class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            @if(count($flatCategories) === 0)
                                <option value="">Cadastre uma categoria primeiro</option>
                            @else
                                @foreach($flatCategories as $category)
                                    <option value="{{ $category['id'] }}" @selected((string) old('category_id') === (string) $category['id'])>{{ $category['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="md:col-span-2 xl:col-span-4">
                        <label class="mb-1 block text-sm font-medium">Descrição</label>
                        <textarea name="description" rows="3" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">{{ old('description') }}</textarea>
                    </div>
                    <div class="md:col-span-2 xl:col-span-2 space-y-2">
                        <label class="mb-1 block text-sm font-medium">Imagem do produto</label>
                        <input id="product-image-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif" class="w-full rounded border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
                        <p id="product-image-empty" class="text-xs text-gray-500 dark:text-gray-400">Selecione uma imagem para visualizar a miniatura antes de salvar.</p>
                        <div id="product-image-preview-wrapper" class="hidden rounded-xl border border-dashed border-gray-300 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-900/50">
                            <img id="product-image-preview" alt="Prévia da imagem selecionada" class="h-40 w-full rounded-lg object-cover">
                            <p id="product-image-name" class="mt-2 text-xs text-gray-600 dark:text-gray-300"></p>
                        </div>
                        @error('image')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" @disabled(count($flatCategories) === 0)>Salvar produto</button>
                    @if(count($flatCategories) === 0)
                        <p class="text-sm text-amber-600 dark:text-amber-400">Cadastre uma categoria antes de criar produtos.</p>
                    @endif
                </div>
            </form>

            <div class="rounded-xl border border-gray-200 bg-white shadow overflow-auto dark:border-gray-700 dark:bg-gray-800">
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

        const imageInput = document.getElementById('product-image-input');
        const previewWrapper = document.getElementById('product-image-preview-wrapper');
        const previewImage = document.getElementById('product-image-preview');
        const previewName = document.getElementById('product-image-name');
        const emptyState = document.getElementById('product-image-empty');

        imageInput?.addEventListener('change', (event) => {
            const [file] = event.target.files ?? [];

            if (!file) {
                previewImage.removeAttribute('src');
                previewName.textContent = '';
                previewWrapper.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            previewImage.src = URL.createObjectURL(file);
            previewName.textContent = `${file.name} • ${Math.round(file.size / 1024)} KB`;
            previewWrapper.classList.remove('hidden');
            emptyState.classList.add('hidden');
        });
    </script>
</x-app-layout>

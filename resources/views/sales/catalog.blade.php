<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Venda por Produto</h2></x-slot>
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
            <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                Pesquise por produto/código, filtre por categoria e abra a categoria para ver os itens. A imagem abre o produto específico.
            </p>
            <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-5">
                <div class="md:col-span-2">
                    <label for="search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Buscar produto</label>
                    <input id="search" name="search" value="{{ $search }}" placeholder="Nome ou código interno" class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label for="category_id" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Categoria</label>
                    <select id="category_id" name="category_id" class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryId === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="stock_filter" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estoque</label>
                    <select id="stock_filter" name="stock_filter" class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        <option value="" @selected($selectedStockFilter === '')>Todos</option>
                        <option value="low" @selected($selectedStockFilter === 'low')>Baixo estoque</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Filtrar</button>
                    <a href="{{ route('sales.catalog') }}" class="rounded bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">Limpar</a>
                </div>
            </form>
        </div>

        @forelse($groupedProducts as $categoryName => $categoryProducts)
            <details class="rounded-lg border border-gray-200 bg-white shadow open:shadow-md dark:border-gray-700 dark:bg-gray-800" open>
                <summary class="cursor-pointer list-none border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $categoryName }}</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $categoryProducts->count() }} produto(s)</span>
                    </div>
                </summary>

                <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($categoryProducts as $product)
                        <article class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/60">
                            <a href="{{ route('sales.product', $product) }}" class="block">
                                @if($product->image_path)
                                    <img src="{{ route('products.image', $product) }}" alt="Imagem de {{ $product->name }}" class="h-36 w-full rounded object-cover">
                                @else
                                    <div class="flex h-36 w-full items-center justify-center rounded bg-gray-200 text-xs font-semibold uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                        Sem imagem
                                    </div>
                                @endif
                            </a>

                            <div class="mt-3 space-y-1">
                                <a href="{{ route('sales.product', $product) }}" class="block font-semibold text-gray-900 hover:underline dark:text-gray-100">{{ $product->name }}</a>
                                <p class="text-xs text-gray-600 dark:text-gray-300">Código: {{ $product->internal_code }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-200">Disponível: <strong id="stock-{{ $product->id }}">{{ $product->stock }}</strong></p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Venda: R$ {{ number_format($product->sale_price,2,',','.') }}</p>
                                @if($product->stock <= $product->minimum_stock)
                                    <p class="text-xs font-semibold uppercase text-red-600 dark:text-red-400">Baixo estoque</p>
                                @endif
                            </div>

                            <form class="mt-3 sale-form space-y-2" data-product-id="{{ $product->id }}">
                                @csrf
                                <label class="text-xs text-gray-600 dark:text-gray-300">Quantidade da venda</label>
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="{{ max(1, (int) $product->stock) }}"
                                    value="1"
                                    class="w-full rounded border border-gray-300 bg-white px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    required
                                >
                                <button
                                    type="submit"
                                    class="w-full rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-500"
                                    @disabled($product->stock < 1)
                                >
                                    {{ $product->stock < 1 ? 'Sem estoque' : 'Vender e emitir NF-e' }}
                                </button>
                                <p class="hidden text-xs text-red-600 sale-error"></p>
                            </form>

                            @if(auth()->user()?->hasRole('dono', 'admin', 'gerente'))
                                <form method="POST" action="{{ route('admin.movements.store') }}" class="mt-3 flex items-end gap-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="type" value="entrada">
                                    <div class="w-full">
                                        <label class="text-xs text-gray-600 dark:text-gray-300">Repor estoque</label>
                                        <input type="number" name="quantity" min="1" value="1" class="w-full rounded border border-gray-300 bg-white px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                                    </div>
                                    <button class="rounded bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Adicionar</button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            </details>
        @empty
            <div class="rounded-lg border border-gray-200 bg-white p-8 text-center text-gray-500 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Nenhum produto encontrado para os filtros selecionados.
            </div>
        @endforelse
    </div>

    <script>
        document.querySelectorAll('.sale-form').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('button[type="submit"]');
                const error = form.querySelector('.sale-error');
                const quantity = Number(form.querySelector('input[name="quantity"]').value);
                const productId = Number(form.dataset.productId);

                error.classList.add('hidden');
                button.disabled = true;

                try {
                    const response = await fetch('{{ route('sales.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            items: [{ product_id: productId, quantity }],
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message ?? 'Falha ao processar venda.');
                    }

                    const stockNode = document.getElementById(`stock-${productId}`);
                    if (stockNode) {
                        const updated = Math.max(0, Number(stockNode.textContent) - quantity);
                        stockNode.textContent = String(updated);
                    }

                    if (data.invoice_url) {
                        window.open(data.invoice_url, '_blank', 'noopener');
                    }
                } catch (exception) {
                    error.textContent = exception.message;
                    error.classList.remove('hidden');
                } finally {
                    button.disabled = false;
                }
            });
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Venda do Produto</h2>
    </x-slot>

    <div class="p-6">
        <div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
            @if(session('success'))
                <div class="mb-4 rounded-md border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if($product->image_path)
                <img src="{{ route('products.image', $product) }}" alt="Imagem de {{ $product->name }}" class="mb-4 h-64 w-full rounded object-cover">
            @else
                <div class="mb-4 flex h-64 w-full items-center justify-center rounded bg-gray-200 text-xs font-semibold uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                    Sem imagem
                </div>
            @endif

            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">Código: {{ $product->internal_code }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">Categoria: {{ $product->category?->name ?? 'Sem categoria' }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">Em estoque: <strong id="stock-{{ $product->id }}">{{ $product->stock }}</strong></p>
            <p class="text-sm text-gray-700 dark:text-gray-300">Valor de venda: R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</p>

            <form class="mt-4 space-y-3" id="single-sale-form" data-product-id="{{ $product->id }}">
                @csrf
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade vendida</label>
                <input
                    type="number"
                    name="quantity"
                    min="1"
                    max="{{ max(1, (int) $product->stock) }}"
                    value="1"
                    class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    required
                >
                <button
                    type="submit"
                    class="w-full rounded bg-indigo-600 px-3 py-2 font-semibold text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-500"
                    @disabled($product->stock < 1)
                >
                    {{ $product->stock < 1 ? 'Produto sem estoque' : 'Confirmar venda e abrir NF-e' }}
                </button>
                <p class="hidden text-sm text-red-600" id="single-sale-error"></p>
            </form>

            @if(auth()->user()?->hasRole('dono', 'admin', 'gerente'))
                <form method="POST" action="{{ route('admin.movements.store') }}" class="mt-4 grid grid-cols-1 gap-3 rounded border border-gray-200 p-3 dark:border-gray-700 md:grid-cols-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="type" value="entrada">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Adicionar ao estoque</label>
                        <input type="number" name="quantity" min="1" value="1" class="mt-1 w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full rounded bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Repor</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script>
        const form = document.getElementById('single-sale-form');
        const error = document.getElementById('single-sale-error');

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            const quantity = Number(form.querySelector('input[name="quantity"]').value);
            const productId = Number(form.dataset.productId);
            const stockNode = document.getElementById(`stock-${productId}`);

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
    </script>
</x-app-layout>

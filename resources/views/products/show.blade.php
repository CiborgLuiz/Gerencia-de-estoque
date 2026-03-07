<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Produto #{{ $product->id }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                    Voltar para produtos
                </a>
            </div>

            <section class="grid grid-cols-1 gap-6 rounded-lg border border-gray-200 bg-white p-6 shadow md:grid-cols-2 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    @if($product->image_path)
                        <img
                            src="{{ route('products.image', $product) }}"
                            alt="Imagem de {{ $product->name }}"
                            class="h-80 w-full rounded-md object-cover"
                        >
                    @else
                        <div class="flex h-80 w-full items-center justify-center rounded-md bg-gray-200 text-sm font-semibold uppercase text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                            Sem imagem cadastrada
                        </div>
                    @endif
                </div>

                <div class="space-y-3 text-sm text-gray-700 dark:text-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                    <p><span class="font-semibold">Categoria:</span> {{ $product->category?->name ?? 'Sem categoria' }}</p>
                    <p><span class="font-semibold">Código interno:</span> {{ $product->internal_code }}</p>
                    <p><span class="font-semibold">NCM:</span> {{ $product->ncm }}</p>
                    <p><span class="font-semibold">Status:</span> {{ $product->status }}</p>
                    <p><span class="font-semibold">Preço de compra:</span> R$ {{ number_format((float) $product->purchase_price, 2, ',', '.') }}</p>
                    <p><span class="font-semibold">Preço de venda:</span> R$ {{ number_format((float) ($product->sale_price ?? $product->price), 2, ',', '.') }}</p>
                    <p><span class="font-semibold">Estoque atual:</span> {{ $product->stock }}</p>
                    <p><span class="font-semibold">Estoque mínimo:</span> {{ $product->minimum_stock }}</p>

                    @if($product->description)
                        <p><span class="font-semibold">Descrição:</span> {{ $product->description }}</p>
                    @endif

                    <div class="pt-3">
                        <a href="{{ route('sales.product', $product) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-500">
                            Vender este item
                        </a>
                    </div>

                    @can('delete', $product)
                        <div class="mt-4 rounded-lg border border-red-300 bg-red-50 p-3 dark:border-red-800 dark:bg-red-900/20">
                            <p class="text-xs font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">Zona de exclusão</p>
                            <p class="mt-1 text-xs text-red-700 dark:text-red-300">
                                Para apagar, confirme o código interno: <span class="font-mono">{{ $product->internal_code }}</span>
                            </p>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end">
                                @csrf
                                @method('DELETE')
                                <div class="w-full">
                                    <label class="block text-xs font-semibold text-red-700 dark:text-red-300">Confirmação</label>
                                    <input
                                        name="confirmation"
                                        placeholder="Digite o código interno"
                                        class="mt-1 w-full rounded border border-red-300 bg-white px-3 py-2 text-sm dark:border-red-700 dark:bg-gray-900 dark:text-gray-100"
                                        required
                                    >
                                </div>
                                <button
                                    type="submit"
                                    onclick="return confirm('Deseja realmente apagar este produto?')"
                                    class="rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                                >
                                    Apagar produto
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

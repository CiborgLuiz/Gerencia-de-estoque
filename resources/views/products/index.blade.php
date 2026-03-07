<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Produtos</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="rounded-md border border-green-300 bg-green-100 text-green-700 p-3 dark:border-green-700 dark:bg-green-900/30 dark:text-green-200">{{ session('success') }}</div>
            @endif

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-3">
                    <a href="{{ route('products.manage') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Criar/editar produtos
                    </a>
                </div>
                <form method="GET" class="flex flex-col sm:flex-row items-end gap-3">
                    <div>
                        <label for="category_id" class="block text-sm text-gray-600 dark:text-gray-300">Filtrar por categoria</label>
                        <select id="category_id" name="category_id" class="border-gray-300 rounded-md shadow-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
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

            @forelse($groupedProducts as $categoryName => $categoryProducts)
                <section class="rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
                    <header class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-200">{{ $categoryName }}</h3>
                    </header>

                    <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($categoryProducts as $product)
                            <article class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/60">
                                <a href="{{ route('admin.products.show', $product) }}" class="block">
                                    @if($product->image_path)
                                        <img
                                            src="{{ route('products.image', $product) }}"
                                            alt="Imagem de {{ $product->name }}"
                                            class="h-36 w-full rounded-md object-cover"
                                        >
                                    @else
                                        <div class="flex h-36 w-full items-center justify-center rounded-md bg-gray-200 text-xs font-semibold uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                            Sem imagem
                                        </div>
                                    @endif
                                </a>

                                <div class="mt-3 space-y-2">
                                    <a href="{{ route('admin.products.show', $product) }}" class="block text-base font-semibold text-gray-900 hover:underline dark:text-gray-100">
                                        {{ $product->name }}
                                    </a>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Código: {{ $product->internal_code }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Venda: R$ {{ number_format((float) ($product->sale_price ?? $product->price ?? 0), 2, ',', '.') }}
                                    </p>
                                    <p @class(['text-sm font-semibold', 'text-red-600' => $product->stock <= 5, 'text-gray-700 dark:text-gray-200' => $product->stock > 5])>
                                        Estoque: {{ $product->stock }}
                                    </p>
                                </div>

                                <div class="mt-3 flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.movements.store') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="type" value="entrada">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="rounded bg-green-600 px-3 py-1 text-sm font-semibold text-white">+1</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.movements.store') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="type" value="saida">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="rounded bg-red-600 px-3 py-1 text-sm font-semibold text-white">-1</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="rounded-lg border border-gray-200 bg-white p-8 text-center text-gray-500 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Nenhum produto encontrado.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Catálogo para Venda</h2></x-slot>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($products as $product)
            <div class="bg-white rounded shadow p-4">
                <h3 class="font-semibold">{{ $product->name }}</h3>
                <p>Disponível: {{ $product->stock }}</p>
                <p>Compra: R$ {{ number_format($product->purchase_price,2,',','.') }}</p>
                <p>Venda: R$ {{ number_format($product->sale_price,2,',','.') }}</p>
                <p class="text-sm text-gray-600">{{ $product->description }}</p>
                @if($product->stock <= $product->minimum_stock)
                    <p class="text-red-600 font-semibold">Estoque mínimo atingido</p>
                @endif
            </div>
        @endforeach
    </div>
</x-app-layout>

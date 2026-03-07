<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Nota Fiscal Eletrônica #{{ $invoice->id }}</h2>
    </x-slot>

    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="mx-auto max-w-7xl space-y-6">
            <section class="rounded-xl border-2 border-gray-300 bg-white shadow dark:border-gray-600 dark:bg-gray-800">
                <div class="flex flex-col gap-4 border-b border-gray-200 p-5 md:flex-row md:items-center md:justify-between dark:border-gray-700">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Documento Fiscal</p>
                        <h3 class="text-2xl font-bold">NF-e Nº {{ $invoice->id }}</h3>
                    </div>
                    <div class="text-sm">
                        <p><span class="font-semibold">Data emissão:</span> {{ optional($invoice->authorized_at)->format('d/m/Y H:i') ?? $invoice->created_at->format('d/m/Y H:i') }}</p>
                        <p><span class="font-semibold">Protocolo:</span> {{ $invoice->protocol ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Emitente</p>
                        <p class="text-sm"><span class="font-semibold">Responsável:</span> {{ $invoice->user?->name ?? 'N/A' }}</p>
                        <p class="text-sm"><span class="font-semibold">Status:</span> {{ $invoice->status }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Destinatário</p>
                        <p class="text-sm"><span class="font-semibold">Cliente:</span> {{ $invoice->customer?->name ?? 'Consumidor final' }}</p>
                        <p class="text-sm break-all"><span class="font-semibold">Chave de acesso:</span> {{ $invoice->chave_acesso ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 p-5 dark:border-gray-700">
                    <h4 class="mb-3 text-base font-semibold">Itens da Nota</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-2 text-left">Produto</th>
                                    <th class="px-3 py-2 text-left">Qtd</th>
                                    <th class="px-3 py-2 text-left">Valor Unitário</th>
                                    <th class="px-3 py-2 text-left">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-3 py-2">{{ $item->product?->name ?? 'Produto removido' }}</td>
                                        <td class="px-3 py-2">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-3 py-2">R$ {{ number_format((float) $item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 border-t border-gray-200 p-5 text-sm md:grid-cols-3 dark:border-gray-700">
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Subtotal Produtos</p>
                        <p class="text-lg font-bold">R$ {{ number_format((float) $invoice->total_value, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Tributos</p>
                        <p class="text-lg font-bold">R$ {{ number_format((float) $invoice->total_tax, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <p class="text-xs uppercase text-gray-500 dark:text-gray-400">Valor Total NF-e</p>
                        <p class="text-lg font-bold">R$ {{ number_format((float) $invoice->total_value, 2, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-3 text-lg font-semibold">XML da NF-e</h3>
                <pre class="max-h-96 overflow-auto whitespace-pre-wrap break-all rounded bg-gray-100 p-3 text-xs leading-relaxed dark:bg-gray-900">{{ $invoice->xml }}</pre>
            </section>
        </div>
    </div>
</x-app-layout>

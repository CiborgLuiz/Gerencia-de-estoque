<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nota Fiscal Eletronica #{{ $invoice->id }}</h2>
    </x-slot>

    @php
        $issuerName = config('nfe.company_name') ?: config('app.name');
        $issuerDocument = config('nfe.cnpj') ?: 'Nao configurado';
        $customer = $invoice->customer;
        $issuedAt = $invoice->authorized_at ?? $invoice->created_at;
    @endphp

    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('invoice.partials.tabs')

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('invoices.index') }}" class="inline-flex w-fit items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Voltar para a lista
                </a>
                <button type="button" onclick="window.print()" class="inline-flex w-fit items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-sky-400 dark:text-slate-950 dark:hover:bg-sky-300">
                    Imprimir nota
                </button>
            </div>

            <section class="overflow-hidden rounded-[2rem] border-2 border-slate-300 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-6 py-6 dark:border-slate-800">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500 dark:text-slate-400">Nota Fiscal Eletronica</p>
                            <h3 class="mt-2 text-3xl font-semibold text-slate-900 dark:text-slate-100">NF-e Nº {{ $invoice->id }}</h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Visualizacao completa da nota gerada a partir da venda.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Status</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $invoice->status }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Emissao</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $issuedAt?->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Protocolo</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $invoice->protocol ?: 'Nao informado' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Chave de acesso</p>
                                <p class="mt-2 break-all text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $invoice->chave_acesso ?: 'Nao informada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 px-6 py-6 xl:grid-cols-3">
                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Emitente</p>
                        <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $issuerName }}</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>CNPJ {{ $issuerDocument }}</p>
                            <p>UF {{ config('nfe.state') ?: 'Nao configurada' }}</p>
                            <p>Vendedor {{ $invoice->user?->name ?: 'Nao informado' }}</p>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Destinatario</p>
                        <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $customer?->name ?: 'Consumidor final' }}</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>Documento {{ $customer?->document ?: 'Nao informado' }}</p>
                            <p>Email {{ $customer?->email ?: 'Nao informado' }}</p>
                            <p>Telefone {{ $customer?->phone ?: 'Nao informado' }}</p>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Venda vinculada</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>Venda #{{ $invoice->sale_id ?: 'Nao vinculada' }}</p>
                            <p>Total de itens {{ $invoice->items->sum('quantity') }}</p>
                            <p>Ambiente {{ config('nfe.driver') === 'sped_nfe' ? 'SEFAZ/Sped' : 'Mock interno' }}</p>
                        </div>
                    </section>
                </div>

                <div class="px-6 pb-6">
                    <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-950">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Produto</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Codigos fiscais</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Qtd</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Unitario</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->items as $item)
                                    <tr class="border-t border-slate-200 align-top dark:border-slate-700">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $item->product?->name ?? 'Produto removido' }}</p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Codigo interno {{ $item->product?->internal_code ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-xs leading-6 text-slate-600 dark:text-slate-300">
                                            <p>NCM {{ $item->product?->ncm ?? 'N/A' }}</p>
                                            <p>CFOP {{ $item->product?->cfop ?? 'N/A' }}</p>
                                            <p>CST/CSOSN {{ $item->product?->cst_csosn ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $item->quantity }}</td>
                                        <td class="px-4 py-4 text-slate-700 dark:text-slate-300">R$ {{ number_format((float) $item->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid gap-4 border-t border-slate-200 px-6 py-6 md:grid-cols-3 dark:border-slate-800">
                    <div class="rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-950">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Subtotal produtos</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $invoice->total_value, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-950">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Tributos</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $invoice->total_tax, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-950">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Valor total da NF-e</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $invoice->total_value, 2, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <details open>
                    <summary class="cursor-pointer text-lg font-semibold text-slate-900 dark:text-slate-100">XML da NF-e</summary>
                    <pre class="mt-4 max-h-[32rem] overflow-auto rounded-[1.5rem] bg-slate-100 p-4 text-xs leading-relaxed text-slate-800 dark:bg-slate-950 dark:text-slate-200">{{ $invoice->xml ?: 'Nenhum XML salvo para esta nota.' }}</pre>
                </details>
            </section>
        </div>
    </div>
</x-app-layout>

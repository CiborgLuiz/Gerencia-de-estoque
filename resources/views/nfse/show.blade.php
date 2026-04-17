<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">NFS-e {{ $serviceInvoice->number ?? 'RPS '.$serviceInvoice->rps_number }}</h2>
    </x-slot>

    @php
        $issuerName = config('nfse.company_name') ?: config('app.name');
        $issuerDocument = config('nfse.cnpj') ?: 'Nao configurado';
        $item = $serviceInvoice->items->first();
        $context = $serviceInvoice->service_context ?? [];
        $customer = $serviceInvoice->customer_data ?? [];
        $address = $customer['address'] ?? [];
        $issuedAt = $serviceInvoice->issued_at ?? $serviceInvoice->created_at;
        $fullDescription = $item?->long_description ?: $item?->description ?: 'Descricao nao informada.';
    @endphp

    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="mx-auto max-w-7xl space-y-6">
            @include('invoice.partials.tabs')

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('nfse.index') }}" class="inline-flex w-fit items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Voltar para emissao
                </a>
                <button type="button" onclick="window.print()" class="inline-flex w-fit items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-sky-400 dark:text-slate-950 dark:hover:bg-sky-300">
                    Imprimir nota
                </button>
            </div>

            <section class="overflow-hidden rounded-[2rem] border-2 border-slate-300 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-6 py-6 dark:border-slate-800">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500 dark:text-slate-400">Nota Fiscal de Servico Eletronica</p>
                            <h3 class="mt-2 text-3xl font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoice->number ?? 'RPS '.$serviceInvoice->rps_number }}</h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Espelho completo da nota gerada pelo modulo de NFS-e.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Status</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoice->status }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Emitida em</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $issuedAt?->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Protocolo</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoice->protocol ?: 'Nao informado' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Codigo de verificacao</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoice->verification_code ?: 'Nao informado' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 px-6 py-6 xl:grid-cols-3">
                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Prestador</p>
                        <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $issuerName }}</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>CNPJ {{ $issuerDocument }}</p>
                            <p>IM {{ config('nfse.municipal_registration') ?: 'Nao configurada' }}</p>
                            <p>Responsavel {{ $serviceInvoice->user?->name ?: 'Nao informado' }}</p>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Tomador</p>
                        <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $customer['name'] ?? 'Nao informado' }}</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>Documento {{ $customer['document'] ?? 'Nao informado' }}</p>
                            <p>Email {{ $customer['email'] ?? 'Nao informado' }}</p>
                            <p>Telefone {{ $customer['phone'] ?? 'Nao informado' }}</p>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Local da prestacao</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>{{ $context['country'] ?? 'Brasil' }}</p>
                            <p>{{ ($context['city_name'] ?? null) ?: 'Municipio nao informado' }}</p>
                            <p>{{ ($context['state'] ?? null) ?: 'UF nao informada' }}</p>
                            <p>Codigo IBGE {{ ($context['city_code'] ?? config('nfse.city_code')) ?: 'Nao informado' }}</p>
                        </div>
                    </section>
                </div>

                <div class="grid gap-4 px-6 pb-6 xl:grid-cols-2">
                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Endereco do tomador</p>
                        <div class="mt-3 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            <p>{{ $address['street'] ?? 'Nao informado' }}, {{ $address['number'] ?? 'S/N' }}</p>
                            <p>{{ $address['complement'] ?? 'Sem complemento' }}</p>
                            <p>{{ $address['neighborhood'] ?? 'Bairro nao informado' }}</p>
                            <p>CEP {{ $address['zip_code'] ?? 'Nao informado' }}</p>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Identificacao fiscal do servico</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">RPS</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoice->rps_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Competencia</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ isset($context['competence_date']) ? \Illuminate\Support\Carbon::parse($context['competence_date'])->format('d/m/Y') : 'Nao informada' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Lista servico</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $item?->service_code ?: 'Nao informado' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Trib. nacional</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $item?->national_tax_code ?: ($context['national_tax_code'] ?? 'Nao informado') }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Trib. municipal</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $item?->municipal_tax_code ?: 'Nao informado' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">NBS</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $item?->nbs_code ?: ($context['nbs_code'] ?? 'Nao informado') }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="px-6 pb-6">
                    <section class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Descricao do Servico</p>
                        <div class="mt-4 min-h-48 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 text-lg leading-9 text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 whitespace-pre-line">{{ $fullDescription }}</div>
                    </section>
                </div>

                <div class="px-6 pb-6">
                    <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-950">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Descricao resumida</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Qtd</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Unitario</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">ISS</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceInvoice->items as $serviceItem)
                                    <tr class="border-t border-slate-200 dark:border-slate-700">
                                        <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $serviceItem->description }}</td>
                                        <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $serviceItem->quantity }}</td>
                                        <td class="px-4 py-4 text-slate-700 dark:text-slate-300">R$ {{ number_format((float) $serviceItem->unit_price, 2, ',', '.') }}</td>
                                        <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ number_format((float) $serviceItem->iss_rate, 2, ',', '.') }}%</td>
                                        <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $serviceItem->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid gap-4 border-t border-slate-200 px-6 py-6 md:grid-cols-3 dark:border-slate-800">
                    <div class="rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-950">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Valor dos servicos</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $serviceInvoice->total_value, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-950">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">ISS estimado</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $serviceInvoice->total_tax, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-950">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Valor liquido</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $serviceInvoice->total_value, 2, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <details open>
                    <summary class="cursor-pointer text-lg font-semibold text-slate-900 dark:text-slate-100">XML e retorno tecnico da NFS-e</summary>
                    <pre class="mt-4 max-h-[32rem] overflow-auto rounded-[1.5rem] bg-slate-100 p-4 text-xs leading-relaxed text-slate-800 dark:bg-slate-950 dark:text-slate-200">{{ $serviceInvoice->xml ?: 'Nenhum XML retornado para esta nota.' }}</pre>
                </details>
            </section>
        </div>
    </div>
</x-app-layout>

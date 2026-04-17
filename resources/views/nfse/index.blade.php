<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">NFS-e e Servicos</h2>
    </x-slot>

    @php
        $issuerName = config('nfse.company_name') ?: config('app.name');
        $issuerDocument = config('nfse.cnpj') ?: 'Nao configurado';
        $defaultCityCode = config('nfse.city_code');
        $activeCatalogItems = $catalogItems->where('is_active', true)->values();
        $catalogPayload = $activeCatalogItems->map(static fn ($catalogItem) => [
            'id' => (string) $catalogItem->id,
            'description' => $catalogItem->description,
            'long_description' => $catalogItem->long_description ?: $catalogItem->description,
            'service_code' => $catalogItem->service_code,
            'national_tax_code' => $catalogItem->national_tax_code,
            'municipal_tax_code' => $catalogItem->municipal_tax_code,
            'nbs_code' => $catalogItem->nbs_code,
            'unit_price' => (float) $catalogItem->unit_price,
            'iss_rate' => (float) $catalogItem->iss_rate,
        ])->values()->all();
        $initialState = [
            'serviceCatalogItemId' => (string) old('service_catalog_item_id'),
            'quantity' => (int) old('quantity', 1),
            'overrideDescription' => old('override_description', ''),
            'competenceDate' => old('competence_date', now()->toDateString()),
            'serviceCountry' => old('service_country', 'Brasil'),
            'serviceCityName' => old('service_city_name', ''),
            'serviceCityCode' => old('service_city_code', $defaultCityCode),
            'serviceState' => old('service_state', 'SP'),
            'nationalTaxCode' => old('national_tax_code', ''),
            'nbsCode' => old('nbs_code', ''),
            'customerName' => old('customer_name', ''),
            'customerDocument' => old('customer_document', ''),
            'customerEmail' => old('customer_email', ''),
            'customerPhone' => old('customer_phone', ''),
            'customerAddress' => old('customer_address', ''),
            'customerNumber' => old('customer_number', ''),
            'customerComplement' => old('customer_complement', ''),
            'customerNeighborhood' => old('customer_neighborhood', ''),
            'customerZipCode' => old('customer_zip_code', ''),
            'defaultCityCode' => $defaultCityCode,
        ];
        $nbsOptions = $catalogItems->pluck('nbs_code')->filter()->unique()->values();
    @endphp

    <div class="space-y-6 p-6 text-gray-900 dark:text-gray-100">
        @include('invoice.partials.tabs')

        @if ($errors->any())
            <div class="rounded-2xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200">
                <p class="font-semibold">Nao foi possivel concluir a operacao.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <section
            class="overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-sky-50 shadow-sm dark:border-slate-700 dark:from-slate-950 dark:via-slate-900 dark:to-slate-900"
            x-data="nfsePortal(@js($catalogPayload), @js($initialState))"
        >
            <div class="border-b border-slate-200 px-6 py-6 dark:border-slate-800">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                    <div class="space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-700 dark:text-sky-300">Portal Contribuinte</p>
                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Emissao completa de NFS-e</h3>
                            <p class="mt-2 max-w-3xl text-sm text-slate-600 dark:text-slate-300">
                                Preencha pessoas, servico e valores como no portal oficial. Ao lado, o sistema monta o espelho completo da nota em tempo real.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ([
                            ['numero' => '1', 'titulo' => 'Pessoas', 'descricao' => 'Tomador e contato'],
                            ['numero' => '2', 'titulo' => 'Servico', 'descricao' => 'Local e descricao'],
                            ['numero' => '3', 'titulo' => 'Valores', 'descricao' => 'Quantidade e ISS'],
                            ['numero' => '4', 'titulo' => 'Emitir NFS-e', 'descricao' => 'Conferencia final'],
                        ] as $step)
                            <div class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/60">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white dark:bg-sky-400 dark:text-slate-950">
                                        {{ $step['numero'] }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $step['titulo'] }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $step['descricao'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid gap-6 px-6 py-6 2xl:grid-cols-[minmax(0,1.4fr)_380px]">
                <form method="POST" action="{{ route('nfse.store') }}" class="space-y-6">
                    @csrf

                    <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">1. Pessoas</p>
                                <h4 class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">Tomador do servico</h4>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Esses dados aparecem no cabecalho da NFS-e.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="customer_name" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nome ou razao social</label>
                                <input id="customer_name" name="customer_name" x-model="customerName" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>
                            </div>
                            <div>
                                <label for="customer_document" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">CPF/CNPJ</label>
                                <input id="customer_document" name="customer_document" x-model="customerDocument" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="customer_email" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                                <input id="customer_email" name="customer_email" type="email" x-model="customerEmail" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="customer_phone" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Telefone</label>
                                <input id="customer_phone" name="customer_phone" x-model="customerPhone" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div class="md:col-span-2">
                                <label for="customer_address" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Endereco</label>
                                <input id="customer_address" name="customer_address" x-model="customerAddress" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="customer_number" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Numero</label>
                                <input id="customer_number" name="customer_number" x-model="customerNumber" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="customer_complement" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Complemento</label>
                                <input id="customer_complement" name="customer_complement" x-model="customerComplement" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="customer_neighborhood" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Bairro</label>
                                <input id="customer_neighborhood" name="customer_neighborhood" x-model="customerNeighborhood" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="customer_zip_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">CEP</label>
                                <input id="customer_zip_code" name="customer_zip_code" x-model="customerZipCode" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">2. Servico</p>
                                <h4 class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">Servico prestado</h4>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Escolha um servico-base e complete os campos fiscais.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="service_catalog_item_id" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Servico cadastrado</label>
                                <select id="service_catalog_item_id" name="service_catalog_item_id" x-model="serviceCatalogItemId" x-on:change="applyServiceTemplate()" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>
                                    <option value="">Selecione um servico</option>
                                    @foreach ($activeCatalogItems as $catalogItem)
                                        <option value="{{ $catalogItem->id }}">
                                            {{ $catalogItem->description }} | R$ {{ number_format((float) $catalogItem->unit_price, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="competence_date" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Data de competencia</label>
                                <input id="competence_date" name="competence_date" type="date" x-model="competenceDate" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="service_country" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Pais</label>
                                <input id="service_country" name="service_country" x-model="serviceCountry" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="service_city_name" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Municipio</label>
                                <input id="service_city_name" name="service_city_name" x-model="serviceCityName" placeholder="Ex.: Janauba" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="service_city_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo IBGE do municipio</label>
                                <input id="service_city_code" name="service_city_code" x-model="serviceCityCode" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="service_state" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">UF</label>
                                <input id="service_state" name="service_state" maxlength="2" x-model="serviceState" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm uppercase shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="national_tax_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo de Tributacao Nacional</label>
                                <input id="national_tax_code" name="national_tax_code" x-model="nationalTaxCode" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                            </div>
                            <div>
                                <label for="nbs_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Item da NBS correspondente ao servico prestado</label>
                                <select id="nbs_code" name="nbs_code" x-model="nbsCode" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                                    <option value="">Selecione...</option>
                                    @foreach ($nbsOptions as $nbsCode)
                                        <option value="{{ $nbsCode }}">{{ $nbsCode }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="override_description" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Descricao do Servico</label>
                                <textarea id="override_description" name="override_description" rows="7" x-model="overrideDescription" placeholder="Ex.: VEICULO..., REVISAO..., pecas aplicadas, mao de obra e observacoes." class="w-full rounded-[1.75rem] border border-slate-300 bg-white px-4 py-4 text-base leading-8 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900"></textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">3. Valores</p>
                                <h4 class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">Valores e tributacao</h4>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">O sistema calcula o total e o ISS com base no servico escolhido.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                            <div>
                                <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Quantidade</label>
                                <input id="quantity" name="quantity" type="number" min="1" x-model.number="quantity" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Valor unitario</label>
                                <input type="text" :value="currency(unitPriceValue())" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Aliquota ISS</label>
                                <input type="text" :value="percent(issRateValue())" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Total calculado</label>
                                <input type="text" :value="currency(totalValue())" readonly class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Base de calculo</p>
                                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100" x-text="currency(totalValue())"></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">ISS estimado</p>
                                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100" x-text="currency(taxValue())"></p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-950">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Valor liquido</p>
                                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-slate-100" x-text="currency(totalValue())"></p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">4. Emitir NFS-e</p>
                                <h4 class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">Conferencia final</h4>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                    Revise o espelho da nota. A descricao, os codigos fiscais e os dados do tomador vao para a visualizacao completa da NFS-e.
                                </p>
                            </div>

                            <button class="inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-sky-400 dark:text-slate-950 dark:hover:bg-sky-300" @disabled($activeCatalogItems->isEmpty())>
                                Emitir NFS-e
                            </button>
                        </div>
                    </section>
                </form>

                <aside class="space-y-6 2xl:sticky 2xl:top-6">
                    <section class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Espelho da Nota</p>
                            <h4 class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100">Pre-visualizacao completa da NFS-e</h4>
                        </div>

                        <div class="space-y-4 bg-slate-100 p-5 dark:bg-slate-950">
                            <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4 dark:border-slate-700">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Nota Fiscal de Servico Eletronica</p>
                                        <h5 class="mt-2 text-xl font-semibold text-slate-900 dark:text-slate-100">RPS PREVIA</h5>
                                    </div>
                                    <div class="text-right text-xs text-slate-500 dark:text-slate-400">
                                        <p>Status: rascunho</p>
                                        <p x-text="competenceDate ? `Competencia: ${formatDate(competenceDate)}` : 'Competencia nao definida'"></p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Prestador</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $issuerName }}</p>
                                        <p class="text-sm text-slate-600 dark:text-slate-300">CNPJ {{ $issuerDocument }}</p>
                                        <p class="text-sm text-slate-600 dark:text-slate-300">IM {{ config('nfse.municipal_registration') ?: 'Nao configurada' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Tomador</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100" x-text="customerName || 'Nome do tomador'"></p>
                                        <p class="text-sm text-slate-600 dark:text-slate-300" x-text="customerDocument || 'Documento nao informado'"></p>
                                        <p class="text-sm text-slate-600 dark:text-slate-300" x-text="customerEmail || 'Email nao informado'"></p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Local da prestacao</p>
                                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300" x-text="serviceCountry || 'Brasil'"></p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300" x-text="serviceCityName || 'Municipio nao informado'"></p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300" x-text="serviceState || 'UF nao informada'"></p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Codigos fiscais</p>
                                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300" x-text="`Lista servico: ${selectedItem?.service_code || '---'}`"></p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300" x-text="`Trib. nacional: ${nationalTaxCode || '---'}`"></p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300" x-text="`Trib. municipal: ${selectedItem?.municipal_tax_code || '---'}`"></p>
                                        <p class="text-sm text-slate-700 dark:text-slate-300" x-text="`NBS: ${nbsCode || '---'}`"></p>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Descricao do servico</p>
                                    <div class="mt-3 min-h-44 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-base leading-8 text-slate-900 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                        <template x-for="(line, index) in previewDescriptionLines()" :key="index">
                                            <p x-text="line"></p>
                                        </template>
                                    </div>
                                </div>

                                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-slate-50 dark:bg-slate-950">
                                            <tr>
                                                <th class="px-3 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Item</th>
                                                <th class="px-3 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Qtd</th>
                                                <th class="px-3 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Unitario</th>
                                                <th class="px-3 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-t border-slate-200 dark:border-slate-700">
                                                <td class="px-3 py-3 text-slate-700 dark:text-slate-300" x-text="selectedItem?.description || 'Servico nao selecionado'"></td>
                                                <td class="px-3 py-3 text-slate-700 dark:text-slate-300" x-text="safeQuantity()"></td>
                                                <td class="px-3 py-3 text-slate-700 dark:text-slate-300" x-text="currency(unitPriceValue())"></td>
                                                <td class="px-3 py-3 font-semibold text-slate-900 dark:text-slate-100" x-text="currency(totalValue())"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Valor servicos</p>
                                        <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100" x-text="currency(totalValue())"></p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">ISS</p>
                                        <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100" x-text="currency(taxValue())"></p>
                                    </div>
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Liquido</p>
                                        <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100" x-text="currency(totalValue())"></p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <h4 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Resumo rapido</h4>
                        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-between">
                                <span>Servicos ativos</span>
                                <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $activeCatalogItems->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>NFS-e emitidas</span>
                                <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoices->total() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Ambiente</span>
                                <span class="font-semibold text-slate-900 dark:text-slate-100">{{ config('nfse.driver') === 'ginfes' ? 'GINFES' : 'Mock interno' }}</span>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]">
            <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <form method="POST" action="{{ route('nfse.catalog-items.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-slate-400">Cadastro base</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-900 dark:text-slate-100">Servicos padrao da NFS-e</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Monte aqui os servicos que vao alimentar a emissao completa.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="service_description" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Descricao base do servico</label>
                            <textarea id="service_description" name="description" rows="6" class="w-full rounded-[1.75rem] border border-slate-300 bg-white px-4 py-4 text-base leading-8 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label for="service_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Item lista servico</label>
                            <input id="service_code" name="service_code" value="{{ old('service_code') }}" placeholder="14.01" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>
                        </div>
                        <div>
                            <label for="national_tax_catalog_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo de Tributacao Nacional</label>
                            <input id="national_tax_catalog_code" name="national_tax_code" value="{{ old('national_tax_code') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                        </div>
                        <div>
                            <label for="municipal_tax_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo tributacao municipal</label>
                            <input id="municipal_tax_code" name="municipal_tax_code" value="{{ old('municipal_tax_code') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                        </div>
                        <div>
                            <label for="catalog_nbs_code" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Item NBS</label>
                            <input id="catalog_nbs_code" name="nbs_code" value="{{ old('nbs_code') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900">
                        </div>
                        <div>
                            <label for="unit_price" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Preco base</label>
                            <input id="unit_price" name="unit_price" type="number" step="0.01" min="0" value="{{ old('unit_price') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>
                        </div>
                        <div>
                            <label for="iss_rate" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Aliquota ISS (%)</label>
                            <input id="iss_rate" name="iss_rate" type="number" step="0.01" min="0" max="100" value="{{ old('iss_rate', '2.00') }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:ring-sky-900" required>
                        </div>
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('is_active', true))>
                        Servico ativo para emissao
                    </label>

                    <button class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-sky-400 dark:text-slate-950 dark:hover:bg-sky-300">
                        Salvar servico
                    </button>
                </form>
            </section>

            <section class="space-y-6">
                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Servicos cadastrados</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $catalogItems->count() }} servico(s) no catalogo.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse ($catalogItems as $catalogItem)
                            <article class="rounded-[1.5rem] border border-slate-200 p-5 dark:border-slate-700">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="space-y-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $catalogItem->description }}</h4>
                                            <span @class([
                                                'inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide',
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' => $catalogItem->is_active,
                                                'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' => !$catalogItem->is_active,
                                            ])>
                                                {{ $catalogItem->is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>

                                        <p class="max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-300">
                                            {{ \Illuminate\Support\Str::limit($catalogItem->long_description ?: $catalogItem->description, 220) }}
                                        </p>

                                        <div class="grid gap-2 text-sm text-slate-500 dark:text-slate-400 sm:grid-cols-2">
                                            <p>Lista servico: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $catalogItem->service_code }}</span></p>
                                            <p>Trib. nacional: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $catalogItem->national_tax_code ?: 'Nao informado' }}</span></p>
                                            <p>Trib. municipal: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $catalogItem->municipal_tax_code ?: 'Nao informado' }}</span></p>
                                            <p>NBS: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $catalogItem->nbs_code ?: 'Nao informado' }}</span></p>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-right dark:bg-slate-950">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Preco base</p>
                                        <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $catalogItem->unit_price, 2, ',', '.') }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">ISS {{ number_format((float) $catalogItem->iss_rate, 2, ',', '.') }}%</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-300">
                                Nenhum servico cadastrado para NFS-e.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">NFS-e emitidas</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Historico com acesso ao espelho completo da nota.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-950">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Numero</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Tomador</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Servico</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Valor</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Acoes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($serviceInvoices as $serviceInvoice)
                                    @php
                                        $firstItem = $serviceInvoice->items->first();
                                    @endphp
                                    <tr class="border-t border-slate-200 dark:border-slate-700">
                                        <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceInvoice->number ?? 'RPS '.$serviceInvoice->rps_number }}</td>
                                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $serviceInvoice->customer_data['name'] ?? $serviceInvoice->customer?->name ?? 'Nao informado' }}</td>
                                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($firstItem?->description ?? 'Servico nao identificado', 80) }}</td>
                                        <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">R$ {{ number_format((float) $serviceInvoice->total_value, 2, ',', '.') }}</td>
                                        <td class="px-4 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide',
                                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' => $serviceInvoice->status === 'emitida',
                                                'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' => in_array($serviceInvoice->status, ['processando', 'pendente'], true),
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' => !in_array($serviceInvoice->status, ['emitida', 'processando', 'pendente'], true),
                                            ])>
                                                {{ $serviceInvoice->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <a href="{{ route('nfse.show', $serviceInvoice) }}" class="inline-flex rounded-full bg-sky-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-sky-500">
                                                Abrir nota completa
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-300">Nenhuma NFS-e emitida ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $serviceInvoices->links() }}
                    </div>
                </section>
            </section>
        </div>
    </div>

    <script>
        function nfsePortal(catalogItems, initialState) {
            return {
                catalogItems,
                serviceCatalogItemId: initialState.serviceCatalogItemId || '',
                quantity: Number(initialState.quantity || 1),
                overrideDescription: initialState.overrideDescription || '',
                competenceDate: initialState.competenceDate || '',
                serviceCountry: initialState.serviceCountry || 'Brasil',
                serviceCityName: initialState.serviceCityName || '',
                serviceCityCode: initialState.serviceCityCode || '',
                serviceState: initialState.serviceState || 'SP',
                nationalTaxCode: initialState.nationalTaxCode || '',
                nbsCode: initialState.nbsCode || '',
                customerName: initialState.customerName || '',
                customerDocument: initialState.customerDocument || '',
                customerEmail: initialState.customerEmail || '',
                customerPhone: initialState.customerPhone || '',
                customerAddress: initialState.customerAddress || '',
                customerNumber: initialState.customerNumber || '',
                customerComplement: initialState.customerComplement || '',
                customerNeighborhood: initialState.customerNeighborhood || '',
                customerZipCode: initialState.customerZipCode || '',

                init() {
                    this.applyServiceTemplate();
                },

                get selectedItem() {
                    return this.catalogItems.find((item) => item.id === this.serviceCatalogItemId) || null;
                },

                applyServiceTemplate() {
                    if (!this.selectedItem) {
                        return;
                    }

                    if (!this.overrideDescription.trim()) {
                        this.overrideDescription = this.selectedItem.long_description || this.selectedItem.description || '';
                    }

                    this.nationalTaxCode = this.nationalTaxCode || this.selectedItem.national_tax_code || '';
                    this.nbsCode = this.nbsCode || this.selectedItem.nbs_code || '';
                    this.serviceCityCode = this.serviceCityCode || initialState.defaultCityCode || '';
                },

                safeQuantity() {
                    const quantity = parseInt(this.quantity, 10);
                    return Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
                },

                unitPriceValue() {
                    return Number(this.selectedItem?.unit_price || 0);
                },

                issRateValue() {
                    return Number(this.selectedItem?.iss_rate || 0);
                },

                totalValue() {
                    return this.unitPriceValue() * this.safeQuantity();
                },

                taxValue() {
                    return this.totalValue() * (this.issRateValue() / 100);
                },

                currency(value) {
                    return new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL',
                    }).format(Number(value || 0));
                },

                percent(value) {
                    return `${new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(Number(value || 0))}%`;
                },

                formatDate(value) {
                    if (!value) {
                        return 'Nao informado';
                    }

                    const parts = value.split('-');
                    if (parts.length !== 3) {
                        return value;
                    }

                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                },

                previewDescriptionLines() {
                    const description = (this.overrideDescription || this.selectedItem?.long_description || '').trim();
                    const lines = description
                        .split(/\r?\n/)
                        .map((line) => line.trim())
                        .filter((line) => line.length > 0);

                    return lines.length > 0 ? lines : ['Descreva o servico para visualizar o espelho da nota.'];
                },
            };
        }
    </script>
</x-app-layout>

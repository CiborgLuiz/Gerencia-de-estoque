<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Notas Fiscais Emitidas</h2>
    </x-slot>

    <div class="p-6 space-y-4">
        @if(session('status'))
            <div class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-auto rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left">NF</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Emissão</th>
                        <th class="px-4 py-3 text-left">Vendedor</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Protocolo</th>
                        <th class="px-4 py-3 text-left">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-t border-gray-200 align-top dark:border-gray-700">
                            <td class="px-4 py-3 font-semibold">#{{ $invoice->id }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide',
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' => $invoice->status === 'autorizada',
                                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' => $invoice->status === 'rejeitada' || $invoice->status === 'cancelada',
                                ])>
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ optional($invoice->authorized_at)->format('d/m/Y H:i') ?? $invoice->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $invoice->user?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-semibold">R$ {{ number_format((float) $invoice->total_value,2,',','.') }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $invoice->protocol ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex min-w-72 flex-col gap-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" target="_blank" class="inline-flex w-fit rounded bg-sky-600 px-3 py-1 text-xs font-semibold text-white hover:bg-sky-500">
                                        Abrir nota
                                    </a>
                                    @if($invoice->status === 'autorizada')
                                        <form method="POST" action="{{ route('invoices.cancel', $invoice) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                            @csrf
                                            <input
                                                name="justification"
                                                class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"
                                                placeholder="Justificativa (mínimo 15 caracteres)"
                                                required
                                            >
                                            <button class="rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-500">Cancelar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-300">Nenhuma nota fiscal emitida ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $invoices->links() }}
    </div>
</x-app-layout>

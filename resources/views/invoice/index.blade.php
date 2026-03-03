<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Notas Fiscais</h2></x-slot>
    <div class="p-6 bg-white rounded shadow">
        <table class="min-w-full text-sm">
            <thead><tr><th>ID</th><th>Status</th><th>Total</th><th>Protocolo</th><th>Ação</th></tr></thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr class="border-t">
                        <td>{{ $invoice->id }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>R$ {{ number_format($invoice->total_value,2,',','.') }}</td>
                        <td>{{ $invoice->protocol }}</td>
                        <td>
                            @if($invoice->status === 'autorizada')
                            <form method="POST" action="{{ route('invoices.cancel', $invoice) }}">@csrf
                                <input name="justification" class="border rounded" placeholder="Justificativa mínima 15 caracteres" required>
                                <button class="text-red-600">Cancelar</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</x-app-layout>

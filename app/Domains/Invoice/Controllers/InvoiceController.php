<?php

namespace App\Domains\Invoice\Controllers;

use App\Domains\Invoice\Services\InvoiceService;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function index(): View
    {
        return view('invoice.index', [
            'invoices' => Invoice::with(['customer', 'user'])->latest()->paginate(20),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        return view('invoice.show', [
            'invoice' => $invoice->load(['items.product', 'customer', 'user', 'sale']),
        ]);
    }

    public function cancel(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate(['justification' => ['required', 'string', 'min:15']]);
        $this->invoiceService->cancel($invoice, $data['justification']);

        return back()->with('status', 'NF-e cancelada com sucesso.');
    }
}

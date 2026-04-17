<?php

namespace App\Domains\Nfse\Controllers;

use App\Domains\Nfse\Requests\IssueServiceInvoiceRequest;
use App\Domains\Nfse\Requests\StoreServiceCatalogItemRequest;
use App\Domains\Nfse\Services\NfseService;
use App\Http\Controllers\Controller;
use App\Models\ServiceCatalogItem;
use App\Models\ServiceInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class NfseController extends Controller
{
    public function __construct(private readonly NfseService $nfseService)
    {
    }

    public function index(): View
    {
        return view('nfse.index', [
            'catalogItems' => ServiceCatalogItem::query()
                ->orderByDesc('is_active')
                ->orderBy('description')
                ->get(),
            'serviceInvoices' => ServiceInvoice::query()
                ->with(['customer', 'user', 'items.serviceCatalogItem'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function storeCatalogItem(StoreServiceCatalogItemRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $fullDescription = $this->normalizeMultiline((string) $payload['description']);

        ServiceCatalogItem::query()->create([
            ...collect($payload)->except('description')->toArray(),
            'description' => $this->summarizeDescription($fullDescription),
            'long_description' => $fullDescription,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Servico para NFS-e cadastrado com sucesso.');
    }

    public function issue(IssueServiceInvoiceRequest $request): RedirectResponse
    {
        try {
            $serviceInvoice = $this->nfseService->issue($request->user(), $request->validated());
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'nfse' => 'Erro ao processar NFS-e: '.$exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('nfse.show', $serviceInvoice)
            ->with('status', 'NFS-e gerada com sucesso.');
    }

    public function show(ServiceInvoice $serviceInvoice): View
    {
        return view('nfse.show', [
            'serviceInvoice' => $serviceInvoice->load(['customer', 'user', 'items.serviceCatalogItem']),
        ]);
    }

    private function normalizeMultiline(string $value): string
    {
        return preg_replace("/\r\n?/", "\n", trim($value)) ?? trim($value);
    }

    private function summarizeDescription(string $value): string
    {
        $lines = array_values(array_filter(
            preg_split('/\n+/', $value) ?: [],
            static fn (string $line): bool => trim($line) !== '',
        ));

        $headline = trim($lines[0] ?? $value);
        $summary = count($lines) > 1 ? $headline.' ...' : $headline;

        return Str::limit($summary, 255, '...');
    }
}

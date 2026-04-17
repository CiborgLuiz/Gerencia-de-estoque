<?php

namespace App\Domains\Nfse\Services;

use App\Models\Customer;
use App\Models\ServiceCatalogItem;
use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class NfseService
{
    public function __construct(private readonly GinfesNfseGateway $gateway)
    {
    }

    public function issue(User $user, array $payload): ServiceInvoice
    {
        return DB::transaction(function () use ($user, $payload): ServiceInvoice {
            $catalogItem = ServiceCatalogItem::query()->findOrFail($payload['service_catalog_item_id']);

            if (!$catalogItem->is_active) {
                throw new RuntimeException('O servico selecionado esta inativo.');
            }

            $quantity = (int) $payload['quantity'];
            $unitPriceInCents = $this->toCents((string) $catalogItem->unit_price);
            $totalInCents = $unitPriceInCents * $quantity;
            $totalTaxInCents = (int) round($totalInCents * (((float) $catalogItem->iss_rate) / 100));
            $description = $this->normalizeMultiline(
                trim((string) ($payload['override_description'] ?? '')) !== ''
                    ? (string) $payload['override_description']
                    : (string) ($catalogItem->long_description ?: $catalogItem->description)
            );
            $serviceContext = $this->buildServiceContext($payload, $catalogItem);
            $customer = $this->upsertCustomer($payload);

            $serviceInvoice = ServiceInvoice::create([
                'user_id' => $user->id,
                'customer_id' => $customer?->id,
                'total_value' => $this->fromCents($totalInCents),
                'total_tax' => $this->fromCents($totalTaxInCents),
                'status' => 'pendente',
                'rps_number' => $this->generateRpsNumber(),
                'customer_data' => $this->buildCustomerData($payload),
                'service_context' => $serviceContext,
                'response_payload' => [
                    'mode' => 'pending',
                ],
            ]);

            ServiceInvoiceItem::create([
                'service_invoice_id' => $serviceInvoice->id,
                'service_catalog_item_id' => $catalogItem->id,
                'description' => $this->summarizeDescription($description),
                'long_description' => $description,
                'service_code' => $catalogItem->service_code,
                'national_tax_code' => $serviceContext['national_tax_code'] ?? $catalogItem->national_tax_code,
                'municipal_tax_code' => $catalogItem->municipal_tax_code,
                'nbs_code' => $serviceContext['nbs_code'] ?? $catalogItem->nbs_code,
                'quantity' => $quantity,
                'unit_price' => $this->fromCents($unitPriceInCents),
                'total_price' => $this->fromCents($totalInCents),
                'iss_rate' => $catalogItem->iss_rate,
            ]);

            $serviceInvoice->load(['items.serviceCatalogItem', 'customer', 'user']);

            $response = $this->gateway->issue($serviceInvoice);

            $serviceInvoice->fill([
                'status' => $response['status'] ?? 'pendente',
                'number' => $response['number'] ?? null,
                'protocol' => $response['protocol'] ?? null,
                'verification_code' => $response['verification_code'] ?? null,
                'xml' => $response['xml'] ?? null,
                'response_payload' => $response['response_payload'] ?? null,
                'issued_at' => $response['issued_at'] ?? null,
            ])->save();

            return $serviceInvoice->fresh(['items.serviceCatalogItem', 'customer', 'user']);
        });
    }

    private function upsertCustomer(array $payload): ?Customer
    {
        $document = $this->normalizeDigits($payload['customer_document'] ?? null);
        $email = $this->nullableString($payload['customer_email'] ?? null);

        $attributes = [
            'name' => (string) $payload['customer_name'],
            'document' => $document,
            'email' => $email,
            'phone' => $this->nullableString($payload['customer_phone'] ?? null),
        ];

        if ($document) {
            $customer = Customer::query()->firstOrNew(['document' => $document]);
            $customer->fill($attributes);
            $customer->save();

            return $customer;
        }

        if ($email) {
            $customer = Customer::query()->firstOrNew(['email' => $email]);
            $customer->fill($attributes);
            $customer->save();

            return $customer;
        }

        return Customer::query()->create($attributes);
    }

    private function buildCustomerData(array $payload): array
    {
        return [
            'name' => (string) $payload['customer_name'],
            'document' => $this->normalizeDigits($payload['customer_document'] ?? null),
            'email' => $this->nullableString($payload['customer_email'] ?? null),
            'phone' => $this->nullableString($payload['customer_phone'] ?? null),
            'address' => [
                'street' => $this->nullableString($payload['customer_address'] ?? null),
                'number' => $this->nullableString($payload['customer_number'] ?? null),
                'complement' => $this->nullableString($payload['customer_complement'] ?? null),
                'neighborhood' => $this->nullableString($payload['customer_neighborhood'] ?? null),
                'city_code' => $this->normalizeDigits($payload['customer_city_code'] ?? config('nfse.city_code')),
                'state' => $this->nullableString(isset($payload['customer_state']) ? strtoupper((string) $payload['customer_state']) : null),
                'zip_code' => $this->normalizeDigits($payload['customer_zip_code'] ?? null),
            ],
        ];
    }

    private function buildServiceContext(array $payload, ServiceCatalogItem $catalogItem): array
    {
        return [
            'competence_date' => $payload['competence_date'] ?? now()->toDateString(),
            'country' => $this->nullableString($payload['service_country'] ?? 'Brasil') ?? 'Brasil',
            'city_name' => $this->nullableString($payload['service_city_name'] ?? null),
            'city_code' => $this->normalizeDigits($payload['service_city_code'] ?? config('nfse.city_code')),
            'state' => $this->nullableString(isset($payload['service_state']) ? strtoupper((string) $payload['service_state']) : null),
            'national_tax_code' => $this->nullableString($payload['national_tax_code'] ?? $catalogItem->national_tax_code),
            'nbs_code' => $this->nullableString($payload['nbs_code'] ?? $catalogItem->nbs_code),
        ];
    }

    private function generateRpsNumber(): string
    {
        return now()->format('ymdHis').str_pad((string) random_int(1, 99), 2, '0', STR_PAD_LEFT);
    }

    private function toCents(string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    private function normalizeDigits(?string $value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits !== '' ? $digits : null;
    }

    private function nullableString(?string $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed !== '' ? $trimmed : null;
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

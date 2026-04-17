<?php

namespace App\Domains\Nfse\Services;

use App\Models\ServiceInvoice;
use Illuminate\Support\Carbon;
use RuntimeException;

class GinfesNfseGateway
{
    public function issue(ServiceInvoice $serviceInvoice): array
    {
        if (config('nfse.driver') !== 'ginfes') {
            return $this->mockIssue($serviceInvoice);
        }

        if (
            !class_exists(\NFePHP\Common\Certificate::class)
            || !class_exists(\NFePHP\NFSeGinfes\Rps::class)
            || !class_exists(\NFePHP\NFSeGinfes\Tools::class)
        ) {
            throw new RuntimeException('Pacote sped-nfse-ginfes nao encontrado. Instale a biblioteca e habilite a extensao SOAP para usar NFSE_DRIVER=ginfes.');
        }

        $certificatePath = (string) config('nfse.certificate_path');
        $certificatePassword = (string) config('nfse.certificate_password');
        $certificateContent = $certificatePath ? @file_get_contents($certificatePath) : false;

        if (!$certificateContent) {
            throw new RuntimeException('Certificado A1 nao encontrado em NFSE_CERTIFICATE_PATH.');
        }

        $config = [
            'cnpj' => preg_replace('/\D/', '', (string) config('nfse.cnpj')),
            'im' => (string) config('nfse.municipal_registration'),
            'cmun' => preg_replace('/\D/', '', (string) config('nfse.city_code')),
            'razao' => (string) config('nfse.company_name'),
            'tpamb' => (int) config('nfse.environment', 2),
        ];

        foreach (['cnpj', 'im', 'cmun', 'razao'] as $requiredKey) {
            if ($config[$requiredKey] === '') {
                throw new RuntimeException('Configuracao NFSE_'.strtoupper($requiredKey).' ausente para emissao Ginfes.');
            }
        }

        $certificate = \NFePHP\Common\Certificate::readPfx($certificateContent, $certificatePassword);
        $tools = new \NFePHP\NFSeGinfes\Tools(
            json_encode($config, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            $certificate
        );

        $rps = new \NFePHP\NFSeGinfes\Rps($this->buildRpsData($serviceInvoice));
        $sendResponse = (string) $tools->recepcionarLoteRps([$rps], (string) $serviceInvoice->rps_number);
        $protocol = $this->extractTag($sendResponse, 'Protocolo') ?? $this->extractTag($sendResponse, 'NumeroLote');

        $finalResponse = $sendResponse;
        if ($protocol) {
            try {
                $finalResponse = (string) $tools->consultarLoteRps($protocol);
            } catch (\Throwable) {
                $finalResponse = $sendResponse;
            }
        }

        $number = $this->extractTag($finalResponse, 'NumeroNfse')
            ?? $this->extractTag($finalResponse, 'Numero');

        return [
            'status' => $number ? 'emitida' : 'processando',
            'number' => $number,
            'rps_number' => (string) $serviceInvoice->rps_number,
            'protocol' => $protocol,
            'verification_code' => $this->extractTag($finalResponse, 'CodigoVerificacao'),
            'xml' => $finalResponse,
            'response_payload' => [
                'send_response' => $sendResponse,
                'consult_response' => $finalResponse,
            ],
            'issued_at' => now(),
        ];
    }

    private function buildRpsData(ServiceInvoice $serviceInvoice): \stdClass
    {
        $item = $serviceInvoice->items->first();
        $customer = $serviceInvoice->customer_data ?? [];
        $address = $customer['address'] ?? [];
        $serviceContext = $serviceInvoice->service_context ?? [];
        $document = preg_replace('/\D/', '', (string) ($customer['document'] ?? ''));
        $issRate = ((float) ($item?->iss_rate ?? 0)) / 100;
        $totalValue = (float) $serviceInvoice->total_value;
        $totalTax = (float) $serviceInvoice->total_tax;
        $serviceCityCode = preg_replace('/\D/', '', (string) ($serviceContext['city_code'] ?? config('nfse.city_code')));
        $issuedAt = $serviceInvoice->created_at ?? now();
        $competenceDate = $serviceContext['competence_date'] ?? null;
        $emissionDate = $competenceDate
            ? Carbon::parse($competenceDate)->setTime($issuedAt->hour, $issuedAt->minute, $issuedAt->second)
            : $issuedAt;

        $std = new \stdClass();
        $std->version = '1.00';
        $std->IdentificacaoRps = new \stdClass();
        $std->IdentificacaoRps->Numero = (int) $serviceInvoice->rps_number;
        $std->IdentificacaoRps->Serie = (string) config('nfse.rps_series', 'SERIEA');
        $std->IdentificacaoRps->Tipo = (int) config('nfse.rps_type', 1);
        $std->DataEmissao = $emissionDate->format('Y-m-d\TH:i:s');
        $std->NaturezaOperacao = (int) config('nfse.natureza_operacao', 1);
        $std->RegimeEspecialTributacao = (int) config('nfse.special_tax_regime', 6);
        $std->OptanteSimplesNacional = (int) config('nfse.simple_national_optant', 1);
        $std->IncentivadorCultural = (int) config('nfse.cultural_incentive', 2);
        $std->Status = 1;

        $std->Tomador = new \stdClass();
        if (strlen($document) === 14) {
            $std->Tomador->Cnpj = $document;
        } elseif (strlen($document) === 11) {
            $std->Tomador->Cpf = $document;
        }
        $std->Tomador->RazaoSocial = (string) ($customer['name'] ?? 'Consumidor final');
        if (!empty($customer['email'])) {
            $std->Tomador->Email = (string) $customer['email'];
        }

        if (array_filter($address)) {
            $std->Tomador->Endereco = new \stdClass();
            $std->Tomador->Endereco->Endereco = (string) ($address['street'] ?? 'Nao informado');
            $std->Tomador->Endereco->Numero = (string) ($address['number'] ?? 'S/N');
            $std->Tomador->Endereco->Complemento = (string) ($address['complement'] ?? '');
            $std->Tomador->Endereco->Bairro = (string) ($address['neighborhood'] ?? 'Centro');
            $std->Tomador->Endereco->CodigoMunicipio = (int) preg_replace('/\D/', '', (string) ($address['city_code'] ?? config('nfse.city_code')));
            $std->Tomador->Endereco->Uf = strtoupper((string) ($address['state'] ?? 'SP'));
            $std->Tomador->Endereco->Cep = preg_replace('/\D/', '', (string) ($address['zip_code'] ?? ''));
        }

        $std->Servico = new \stdClass();
        $std->Servico->ItemListaServico = (string) $item?->service_code;
        $std->Servico->CodigoTributacaoMunicipio = (string) ($item?->municipal_tax_code ?: $item?->service_code);
        $std->Servico->Discriminacao = (string) ($item?->long_description ?: $item?->description);
        $std->Servico->CodigoMunicipio = (int) ($serviceCityCode ?: preg_replace('/\D/', '', (string) config('nfse.city_code')));

        $std->Servico->Valores = new \stdClass();
        $std->Servico->Valores->ValorServicos = $totalValue;
        $std->Servico->Valores->ValorDeducoes = 0.0;
        $std->Servico->Valores->ValorPis = 0.0;
        $std->Servico->Valores->ValorCofins = 0.0;
        $std->Servico->Valores->ValorInss = 0.0;
        $std->Servico->Valores->ValorIr = 0.0;
        $std->Servico->Valores->ValorCsll = 0.0;
        $std->Servico->Valores->IssRetido = 2;
        $std->Servico->Valores->ValorIss = $totalTax;
        $std->Servico->Valores->ValorIssRetido = 0.0;
        $std->Servico->Valores->OutrasRetencoes = 0.0;
        $std->Servico->Valores->BaseCalculo = $totalValue;
        $std->Servico->Valores->Aliquota = $issRate;
        $std->Servico->Valores->ValorLiquidoNfse = $totalValue;
        $std->Servico->Valores->DescontoIncondicionado = 0.0;
        $std->Servico->Valores->DescontoCondicionado = 0.0;

        return $std;
    }

    private function mockIssue(ServiceInvoice $serviceInvoice): array
    {
        $xml = $this->buildMockXml($serviceInvoice);

        return [
            'status' => 'emitida',
            'number' => 'NFSE-'.str_pad((string) $serviceInvoice->id, 6, '0', STR_PAD_LEFT),
            'rps_number' => (string) $serviceInvoice->rps_number,
            'protocol' => 'GINFES-HML-'.now()->format('YmdHis'),
            'verification_code' => strtoupper(substr(hash('sha256', $xml), 0, 8)),
            'xml' => $xml,
            'response_payload' => [
                'mode' => 'mock',
                'driver' => config('nfse.driver', 'mock'),
            ],
            'issued_at' => now(),
        ];
    }

    private function buildMockXml(ServiceInvoice $serviceInvoice): string
    {
        $payload = [
            'invoice_id' => $serviceInvoice->id,
            'status' => 'emitida',
            'customer' => $serviceInvoice->customer_data,
            'service_context' => $serviceInvoice->service_context,
            'items' => $serviceInvoice->items->map(fn ($item) => [
                'description' => $item->description,
                'long_description' => $item->long_description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'iss_rate' => $item->iss_rate,
                'service_code' => $item->service_code,
                'national_tax_code' => $item->national_tax_code,
                'municipal_tax_code' => $item->municipal_tax_code,
                'nbs_code' => $item->nbs_code,
            ])->all(),
        ];

        return '<nfse>'.json_encode($payload, JSON_UNESCAPED_UNICODE).'</nfse>';
    }

    private function extractTag(string $xml, string $tag): ?string
    {
        if (!preg_match("/<{$tag}>([^<]+)<\\\/{$tag}>/", $xml, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }
}

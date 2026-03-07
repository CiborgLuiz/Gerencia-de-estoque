<?php

namespace App\Domains\Invoice\Services;

use RuntimeException;

class SefazClient
{
    public function authorize(string $signedXml): array
    {
        if (config('nfe.driver') !== 'sped_nfe') {
            return $this->mockAuthorization($signedXml);
        }

        if (!class_exists(\NFePHP\NFe\Tools::class)) {
            throw new RuntimeException('Pacote nfephp-org/sped-nfe não instalado para modo sped_nfe.');
        }

        $tools = $this->makeTools();

        if (!method_exists($tools, 'sefazEnviaLote')) {
            throw new RuntimeException('Método sefazEnviaLote não disponível na versão atual do sped-nfe.');
        }

        $idLote = str_pad((string) random_int(1, 999999999999999), 15, '0', STR_PAD_LEFT);
        $responseXml = (string) $tools->sefazEnviaLote([$signedXml], $idLote);

        return [
            'status' => $this->extractStatus($responseXml),
            'protocol' => $this->extractTag($responseXml, 'nProt'),
            'chave_acesso' => $this->extractTag($responseXml, 'chNFe'),
            'xml' => $signedXml,
            'sefaz_response' => $responseXml,
        ];
    }

    public function cancel(string $chaveAcesso, string $justification): array
    {
        if (config('nfe.driver') !== 'sped_nfe') {
            return $this->mockCancel($chaveAcesso, $justification);
        }

        if (!class_exists(\NFePHP\NFe\Tools::class)) {
            throw new RuntimeException('Pacote nfephp-org/sped-nfe não instalado para modo sped_nfe.');
        }

        $tools = $this->makeTools();
        if (!method_exists($tools, 'sefazCancela')) {
            throw new RuntimeException('Método sefazCancela não disponível na versão atual do sped-nfe.');
        }

        $responseXml = (string) $tools->sefazCancela($chaveAcesso, $justification, 1);

        return [
            'status' => str_contains($responseXml, '<cStat>135</cStat>') ? 'cancelada' : 'rejeitada',
            'protocol' => $this->extractTag($responseXml, 'nProt') ?? ('CANCEL-'.now()->format('YmdHis')),
            'message' => $this->extractTag($responseXml, 'xMotivo') ?? $justification,
            'chave_acesso' => $chaveAcesso,
            'sefaz_response' => $responseXml,
        ];
    }

    private function mockAuthorization(string $signedXml): array
    {
        return [
            'status' => 'autorizada',
            'protocol' => 'HOMOLOGACAO-'.now()->format('YmdHis'),
            'chave_acesso' => hash('sha256', $signedXml),
            'xml' => $signedXml,
        ];
    }

    private function mockCancel(string $chaveAcesso, string $justification): array
    {
        return [
            'status' => 'cancelada',
            'protocol' => 'CANCEL-'.now()->format('YmdHis'),
            'message' => $justification,
            'chave_acesso' => $chaveAcesso,
        ];
    }

    private function makeTools(): object
    {
        $certificatePath = (string) config('nfe.certificate_path');
        $certificatePassword = (string) config('nfe.certificate_password');
        $certificate = $certificatePath ? @file_get_contents($certificatePath) : false;

        if (!$certificate) {
            throw new RuntimeException('Certificado A1 não encontrado em NFE_CERTIFICATE_PATH.');
        }

        if (!class_exists(\NFePHP\Common\Certificate::class)) {
            throw new RuntimeException('Classe NFePHP\\Common\\Certificate não encontrada.');
        }

        $config = [
            'atualizacao' => date('Y-m-d H:i:s'),
            'tpAmb' => (int) config('nfe.environment', 2),
            'razaosocial' => (string) config('nfe.company_name', 'Empresa Homologacao'),
            'siglaUF' => (string) config('nfe.state', 'SP'),
            'cnpj' => preg_replace('/\D/', '', (string) config('nfe.cnpj')),
            'schemes' => 'PL_009_V4',
            'versao' => '4.00',
        ];

        $certificateObject = \NFePHP\Common\Certificate::readPfx($certificate, $certificatePassword);

        return new \NFePHP\NFe\Tools(json_encode($config, JSON_UNESCAPED_UNICODE), $certificateObject);
    }

    private function extractStatus(string $xml): string
    {
        return str_contains($xml, '<cStat>100</cStat>') ? 'autorizada' : 'rejeitada';
    }

    private function extractTag(string $xml, string $tag): ?string
    {
        if (!preg_match("/<{$tag}>([^<]+)<\\\/{$tag}>/", $xml, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }
}

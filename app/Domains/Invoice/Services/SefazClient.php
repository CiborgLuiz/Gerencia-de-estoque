<?php

namespace App\Domains\Invoice\Services;

class SefazClient
{
    public function authorize(string $signedXml): array
    {
        // Ponto de integração com NFePHP/SEFAZ SOAP.
        return [
            'status' => 'autorizada',
            'protocol' => 'HOMOLOGACAO-'.now()->format('YmdHis'),
            'chave_acesso' => hash('sha256', $signedXml),
            'xml' => $signedXml,
        ];
    }

    public function cancel(string $chaveAcesso, string $justification): array
    {
        return [
            'status' => 'cancelada',
            'protocol' => 'CANCEL-'.now()->format('YmdHis'),
            'message' => $justification,
            'chave_acesso' => $chaveAcesso,
        ];
    }
}

<?php

return [
    'driver' => env('NFE_DRIVER', 'mock'),
    'certificate_path' => env('NFE_CERTIFICATE_PATH'),
    'certificate_password' => env('NFE_CERTIFICATE_PASSWORD'),
    'cnpj' => env('NFE_CNPJ'),
    'company_name' => env('NFE_COMPANY_NAME'),
    'state' => env('NFE_STATE', 'SP'),
    'environment' => (int) env('NFE_ENVIRONMENT', 2), // 1=producao, 2=homologacao
];

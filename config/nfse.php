<?php

return [
    'driver' => env('NFSE_DRIVER', 'mock'),
    'certificate_path' => env('NFSE_CERTIFICATE_PATH'),
    'certificate_password' => env('NFSE_CERTIFICATE_PASSWORD'),
    'cnpj' => env('NFSE_CNPJ'),
    'company_name' => env('NFSE_COMPANY_NAME'),
    'municipal_registration' => env('NFSE_MUNICIPAL_REGISTRATION'),
    'city_code' => env('NFSE_CITY_CODE'),
    'environment' => (int) env('NFSE_ENVIRONMENT', 2),
    'rps_series' => env('NFSE_RPS_SERIES', 'SERIEA'),
    'rps_type' => (int) env('NFSE_RPS_TYPE', 1),
    'natureza_operacao' => (int) env('NFSE_NATUREZA_OPERACAO', 1),
    'special_tax_regime' => (int) env('NFSE_SPECIAL_TAX_REGIME', 6),
    'simple_national_optant' => (int) env('NFSE_SIMPLE_NATIONAL_OPTANT', 1),
    'cultural_incentive' => (int) env('NFSE_CULTURAL_INCENTIVE', 2),
];

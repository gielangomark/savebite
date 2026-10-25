<?php
return [
    'merchant_code' => env('DUITKU_MERCHANT_CODE'),
    'api_key' => env('DUITKU_API_KEY'),
    'is_production' => env('DUITKU_IS_PRODUCTION', false),
    'default_payment_method' => env('DUITKU_DEFAULT_PAYMENT_METHOD', 'BC'),
    'disbursement_enabled' => env('DUITKU_DISBURSEMENT_ENABLED', false),
];

<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'duration_restriction' => 30,
    'sms_limit' => 12,
    'otp_length' => 5,
    'otp_default_value' => '88888',
    'otp_template_name' => 'otp',

    /*
    |--------------------------------------------------------------------------
    | Default SMS driver
    |--------------------------------------------------------------------------
    */
    'default' => env('SMS_DRIVER', 'kavenegar'),

    'drivers' => [
        'kavenegar' => [
            'api_key' => env('KAVENEGAR_API_KEY', env('KAVENEGAR_KEY')),
            'sender' => env('KAVENEGAR_SENDER', null),
        ],
        'smsir' => [
            'api_key' => env('SMSIR_API_KEY'),
            'line_number' => env('SMSIR_LINE_NUMBER', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Phone number attribute name for "Send SMS" action (e.g. User model)
    |--------------------------------------------------------------------------
    */
    'receptor_attribute' => env('SMS_MANAGER_RECEPTOR_ATTRIBUTE', 'cellphone'),
];
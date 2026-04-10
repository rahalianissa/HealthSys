<?php

return [
    'sms_enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
    'channels' => [
        'mail' => ['enabled' => true],
        'database' => ['enabled' => true],
        'broadcast' => ['enabled' => env('BROADCAST_NOTIFICATIONS_ENABLED', true)],
        'sms' => [
            'enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
            'provider' => env('SMS_PROVIDER', 'vonage'),
        ],
    ],
    'reminder' => [
        'hours_before' => env('REMINDER_HOURS_BEFORE', 24),
        'batch_size' => env('REMINDER_BATCH_SIZE', 100),
    ],
];
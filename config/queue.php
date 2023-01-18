<?php

return [
    'connections' => [
        /*
        |--------------------------------------------------------------------------
        | Laravel SQS FIFO Configuration
        |--------------------------------------------------------------------------
        |
        | Configs of the queue driver.
        |
        */
        'sqsfifo' => [
            'driver' => 'sqsfifo',
            'key' => (env('SQS_STACK') == 'sqs') ? env('AWS_ACCESS_KEY_ID') : null,
            'secret' => (env('SQS_STACK') == 'sqs') ? env('AWS_SECRET_ACCESS_KEY') : null,
            'credentials' => (env('SQS_STACK') == 'sqs') ? true : false,
            'endpoint' => env('SQS_ENDPOINT'),
            'prefix' => env('SQS_PREFIX'),
            'suffix' => env('SQS_SUFFIX'),
            'queue' => env('SQS_QUEUE'),
            'queue_name_prefix' => env('SQS_QUEUE_NAME_PREFIX', ''),
            'message_group_id' => env('SQS_MESSAGE_GROUP_ID', env('SQS_PREFIX') . "/" . env('SQS_QUEUE')),
            'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-2'),
        ]
    ]
];
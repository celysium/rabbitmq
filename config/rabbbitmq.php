<?php

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

return [
    'host'     => env('RABBITMQ_HOST', ''),
    'port'     => env('RABBITMQ_PORT', 5672),
    'user'     => env('RABBITMQ_USER', ''),
    'password' => env('RABBITMQ_PASSWORD', ''),
    'vhost'    => env('RABBITMQ_VHOST', '/'),
    'queue'    => env('RABBITMQ_QUEUE', 'default'),
    'exchange' => [
        'name' => env('RABBITMQ_EXCHANGE_NAME', ''),
        'key'  => env('RABBITMQ_EXCHANGE_KEY', ''),
        'type' => env('RABBITMQ_EXCHANGE_TYPE', AMQPExchangeType::DIRECT),
    ],
    'message'  => [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT
    ]
];

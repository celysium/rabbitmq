<?php

namespace Celysium\RabbitMQ\Console\Commands;

use Celysium\RabbitMQ\Facades\RabbitMQ;
use Illuminate\Console\Command;

class ConsumeCommand extends Command
{
    protected $signature = 'rabbitmq:consume';

    protected $description = 'Listener RabbitMQ queue.';

    public function handle(): void
    {
        RabbitMQ::consume();
    }
}

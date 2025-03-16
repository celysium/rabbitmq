<?php

namespace Celysium\RabbitMQ;

use Celysium\RabbitMQ\Console\Commands\ConsumeCommand;
use Celysium\RabbitMQ\Events\PublishMessageEvent;
use Celysium\RabbitMQ\Listeners\PublishMessageListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class RabbitMQServiceProvider extends ServiceProvider
{
    public function register()
    {
        Event::listen(PublishMessageEvent::class, PublishMessageListener::class);

        $this->app->bind('rabbitmq', function () {
            return new RabbitMQ();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/rabbitmq.php', 'rabbitmq');

        $this->publishes([
            __DIR__ . '/../config/rabbitmq.php' => config_path('rabbitmq.php'),
        ], 'config');

        $this->commands(ConsumeCommand::class);
    }
}

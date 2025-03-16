<?php

namespace Celysium\RabbitMQ;

use Celysium\RabbitMQ\Contracts\MessageBrokerInterface;
use Celysium\RabbitMQ\Events\IncomingMessageEvent;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements MessageBrokerInterface
{
    private object $config;
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->loadConfig();
        $this->connect();
        $this->declare();
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    public function loadConfig(array $config = [])
    {
        $this->config = json_decode(json_encode(array_merge(config('rabbitmq'), $config)));
    }

    /**
     * @throws Exception
     */
    private function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config->host,
            $this->config->port,
            $this->config->user,
            $this->config->password,
            $this->config->vhost
        );
    }

    /**
     * @throws Exception
     */
    private function disconnect()
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function declare()
    {
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->config->exchange->name, $this->config->exchange->type, false, true, false);
        $this->channel->queue_declare($this->config->queue, false, true, false, false);
        $this->channel->queue_bind($this->config->queue, $this->config->exchange->name, $this->config->exchange->key);
    }

    /**
     * @param Message $message
     * @param callable|null $ack
     * @param callable|null $nack
     * @return void
     * @throws Exception
     */
    public function publish(Message $message, callable $ack = null, callable $nack = null): void
    {

        $this->channel->confirm_select();
        if ($ack) {
            $this->channel->set_ack_handler($ack);
        }
        if ($nack) {
            $this->channel->set_nack_handler($nack);
        }

        $this->send($message);
        $this->channel->wait_for_pending_acks();
    }

    /**
     * @param Message $message
     * @return void
     */
    public function send(Message $message)
    {
        $amqpMessage = new AMQPMessage($message->getBody(), ['delivery_mode' => $this->config->message->delivery_mode, 'headers' => $message->getHeaders()]);
        foreach ($message->getReceivers() as $receiver) {
            $this->channel->basic_publish($amqpMessage, $this->config->exchange->name, $receiver);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function consume(): void
    {
        $callback = function (AMQPMessage $amqpMessage) {
            echo sprintf("[%s] Received message : %s\n", now(), $amqpMessage->getBody());

            event(new IncomingMessageEvent(Message::resolve($amqpMessage->getBody(), $amqpMessage->get('headers')->getNativeData())));
            $amqpMessage->ack();
        };

        $this->channel->basic_consume($this->config->queue, '', false, false, false, false, $callback);

        echo sprintf("[%s] ready for gat new message : %s\n", now(), $this->config->queue);
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * @param Message[] $messages
     * @param callable|null $ack
     * @param callable|null $nack
     * @return void
     * @throws Exception
     */
    public function batch(array $messages, callable $ack = null, callable $nack = null)
    {
        $this->channel->confirm_select();
        if ($ack) {
            $this->channel->set_ack_handler($ack);
        }
        if ($nack) {
            $this->channel->set_nack_handler($nack);
        }

        foreach ($messages as $message) {
            $this->send($message);
        }
        $this->channel->publish_batch();

        $this->channel->wait_for_pending_acks();
    }

    /**
     * @param Message[] $messages
     * @return void
     * @throws Exception
     */
    public function transaction(array $messages)
    {
        $this->channel->tx_select();

        foreach ($messages as $message) {
            $this->send($message);
        }

        $this->channel->tx_commit();
    }
}

<?php

namespace Celysium\RabbitMQ;
use Illuminate\Support\Arr;


class Message
{
    private array $headers;
    private array $data;
    private array $receivers;

    /**
     * @param array $data
     * @param array $headers
     */
    public function __construct(array $data, array $headers = [])
    {
        $this->data     = $data;
        $this->headers  = $headers;
    }

    /**
     * @param array $data
     * @param array $headers
     * @return Message
     */
    public static function make(array $data, array $headers = []): Message
    {
        return new self($headers, $data);
    }

    /**
     * @param string $body
     * @param array $headers
     * @return Message
     */
    public static function resolve(string $body, array $headers): Message
    {
        return new self(json_decode($body, true), $headers);
    }

    /**
     * @param string $key
     * @param string|int|null $default
     * @return mixed
     */
    public function getHeaders(string $key = '*', $default = null): mixed
    {
        return $key == '*' ? $this->headers : Arr::get($this->headers, $key, $default);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return json_encode($this->data);
    }

    /**
     * @param ...$receivers
     * @return $this
     */
    public function receivers(...$receivers): Message
    {
        $this->receivers = $receivers;
        return $this;
    }

    /**
     * @return array
     */
    public function getReceivers(): array
    {
        return $this->receivers;
    }
}

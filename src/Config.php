<?php namespace Starcorn;


require_once('src/protocol/H11Protocol.php');


use Starcorn\abstract\{AbstractProtocol, AbstractServer};
use Starcorn\enum\{EnumServer, EnumProtocol};


final class Config
{
    protected $app;
    protected string $host;
    protected int $port;
    protected int $worker;
    protected int $socket_type;
    protected int $socket_domain;
    protected int $socket_backlog;
    protected int $socket_protocol;
    protected int $socket_read_length;
    protected int $timeout_second;
    protected int $timeout_millisecond;
    protected AbstractServer $server;
    protected AbstractProtocol $http_protocol;

    public function __construct(callable $app,
                                string $host,
                                int $port,
                                int $worker,
                                int $socket_type,
                                int $socket_domain,
                                int $socket_backlog,
                                int $socket_protocol,
                                int $socket_read_length,
                                int $timeout_second,
                                int $timeout_millisecond,
                                EnumServer $server,
                                EnumProtocol $http_protocol)
    {
        $this->app = $app;
        $this->host = $host;
        $this->port = $port;
        $this->worker = $worker;
        $this->socket_type = $socket_type;
        $this->socket_domain = $socket_domain;
        $this->socket_backlog = $socket_backlog;
        $this->socket_protocol = $socket_protocol;
        $this->socket_read_length = $socket_read_length;
        $this->timeout_second = $timeout_second;
        $this->timeout_millisecond = $timeout_millisecond;

        $this->server = new $server->value($this);
        $this->http_protocol = new $http_protocol->value($this);
    }

    public function app(): callable
    {
        return $this->app;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function worker(): int
    {
        return $this->worker;
    }

    public function socket_type(): int
    {
        return $this->socket_type;
    }

    public function socket_domain(): int
    {
        return $this->socket_domain;
    }

    public function socket_backlog(): int
    {
        return $this->socket_backlog;
    }

    public function socket_protocol(): int
    {
        return $this->socket_protocol;
    }

    public function socket_read_length(): int
    {
        return $this->socket_read_length;
    }

    public function timeout_second(): int
    {
        return $this->timeout_second;
    }

    public function timeout_millisecond(): int
    {
        return $this->timeout_millisecond;
    }

    public function server(): AbstractServer
    {
        return $this->server;
    }

    public function http_protocol(): AbstractProtocol {
        return $this->http_protocol;
    }
}


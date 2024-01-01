<?php namespace Starcorn;


use Socket;


final class StarSocket
{
    protected Socket $socket;

    public function __construct(int $domain, int $type, int $protocol)
    {
        $socket = socket_create($domain, $type, $protocol);
        if($socket === false) exit();
        $this->socket = $socket;
    }

    public function socket(): Socket
    {
        return $this->socket;
    }

    public function bind(string $address, int $port = 0): void
    {
        socket_bind($this->socket, $address, $port);
    }

    public function listen(int $backlog = 0): void
    {
        socket_listen($this->socket, $backlog);
    }

    public function accept(): Socket | false
    {
        return socket_accept($this->socket);
    }

    public static function read(Socket $socket,
                                int $length,
                                int $mode = PHP_BINARY_READ): string | false
    {
        return socket_read($socket, $length, $mode);
    }

    public static function write(Socket $socket,
                                 string $data,
                                 int $length): int | false
    {
        return socket_write($socket, $data, $length);
    }

    public static function select(?array &$read,
                                  ?array &$write,
                                  ?array &$except,
                                  int $second,
                                  int $millisecond): int
    {
        $result = socket_select($read, $write, $except, $second, $millisecond);
        if($result === false) exit();
        return $result;
    }

    public static function close(Socket $socket): void
    {
        socket_close($socket);
    }
}



<?php namespace Starcorn\protocol;


require_once(__DIR__.'/../StarSocket.php');
require_once(__DIR__.'/../abstract/AbstractProtocol.php');


use Starcorn\StarSocket;
use Starcorn\abstract\AbstractProtocol;


final class H11Protocol extends AbstractProtocol
{
    protected static string $sep = "\r\n";
    protected string $protocol = 'HTTP/1.1';

    protected function init(): void
    {
        $this->socket = new StarSocket(domain: self::$config->socket_domain(),
                                       type: self::$config->socket_type(),
                                       protocol: self::$config->socket_protocol());

        $this->socket->bind(address: self::$config->host(), port: self::$config->port());
        $this->socket->listen(self::$config->socket_backlog());
    }

    public function request_handler(string $id): bool
    {
        [$row, $buffer] = explode(self::$sep, $this->clients_buffer[$id], 2);
        [$method, $path, $protocol] = explode(' ', $row);

        if($this->protocol !== $protocol) return false;
        [$headers, $body] = self::parse_buffer($buffer);

        $app = self::$config->app();
        $content = $app($method, $path, $protocol, $headers, $body);
        $this->clients_write[$id] = $this->clients_read[$id];
        $this->set_message($id, $content);
        unset($this->clients_read[$id], $this->clients_buffer[$id]);
        return true;
    }

    public function connect(string $key): void
    {
        $socket = $this->socket->accept();
        $this->clients_read[$key] = $socket;
    }

    public function disconnect(string $id, \Socket $client): void {
        $this->socket::close($client);
        unset(
            $this->clients_read[$id],
            $this->clients_write[$id],
            $this->clients_except[$id],
            $this->clients_buffer[$id],
            $this->clients_message[$id]
        );
    }

    public function set_buffer(string $id, string $value): void
    {
        @$this->clients_buffer[$id] .= $value;
    }

    public function get_buffer(string $id): ?string
    {
        if(array_key_exists($id, $this->clients_buffer))
        {
            return $this->clients_buffer[$id];
        }
        return null;
    }

    public function set_message(string $id, string $value): void
    {
        $this->clients_message[$id] = $value;
    }

    public function get_message(string $id): string
    {
        return $this->clients_message[$id];
    }

    protected static function parse_buffer(string $buffer): array
    {
        $body = '';
        $headers = array();

        while($buffer)
        {
            [$row, $buffer] = explode(self::$sep, $buffer, 2);
            @[$name, $value] = explode(":", $row);

            if($name and $value) $headers[trim($name)] = trim($value);
            else
            {
                $body = trim($buffer);
                break;
            }
        }
        return [$headers, $body];
    }
}

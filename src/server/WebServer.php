<?php namespace Starcorn\server;


require_once('src/StarSocket.php');
require_once('src/abstract/AbstractServer.php');


use Starcorn\StarSocket;
use Starcorn\abstract\{AbstractServer, AbstractProtocol};


final class WebServer extends AbstractServer
{
    public function run(): void
    {
        $protocol = self::$config->http_protocol();
        $this->event_loop($protocol);
    }

    private function event_loop(AbstractProtocol $protocol): void
    {
        $client_id = 0;
        $main_socket = $protocol->socket();

        while(true)
        {
            [$clients_read, $clients_write, $clients_except] = $protocol->clients();
            $read = [self::$socket_name => $main_socket->socket(), ...$clients_read];
            $write = [...$clients_write];
            $except = [...$clients_except];

            StarSocket::select(
                $read,
                $write,
                $except,
                self::$config->timeout_second(),
                self::$config->timeout_millisecond()
            );

            if(array_key_exists(self::$socket_name, $read))
            {
                $protocol->connect(self::$client_name."$client_id");
                $client_id++;
                unset($read[self::$socket_name]);
            }

            $this->read_connections($read, $clients_read, $protocol);
            $this->write_connections($write, $protocol);
        }
    }

    private function read_connections(array $read, array &$clients_read, AbstractProtocol $protocol): void
    {
        foreach($read as $id => $client)
        {
            $buffer = StarSocket::read($client, self::$config->socket_read_length());
            if($buffer)
            {
                $protocol->set_buffer($id, $buffer);
                unset($clients_read[$id]);
            }
            else $protocol->disconnect($id, $client);
        }

        foreach($clients_read as $id => $client)
        {
            $data = $protocol->get_buffer($id);
            if($data !== null) $protocol->request_handler($id);
        }
    }

    private function write_connections(array $write, AbstractProtocol $protocol): void
    {
        foreach($write as $id => $client)
        {
            $data = $protocol->get_message($id);
            $ref = StarSocket::write($client, $data, self::$config->socket_read_length());
            if($ref) $protocol->set_message($id, substr($data, $ref));
            else $protocol->disconnect($id, $client);
        }
    }
}
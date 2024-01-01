<?php namespace Starcorn;


require_once('Config.php');
require_once('src/server/WebServer.php');
require_once('src/enum/EnumServer.php');
require_once('src/enum/EnumProtocol.php');


use Starcorn\enum\EnumServer;
use Starcorn\enum\EnumProtocol;


function run(callable $app,
             ?string $host = null,
             ?int $port = null,
             int $worker = 1,
             int $socket_type = SOCK_STREAM,
             ?int $socket_domain = null,
             int $socket_backlog = 15,
             int $socket_protocol = SOL_TCP,
             int $socket_read_length = 2048,
             int $timeout_second = 0,
             int $timeout_millisecond = 0,
             EnumServer $server = EnumServer::webserver,
             EnumProtocol $http_protocol = EnumProtocol::h11protocol): void
{

    if($socket_domain === null and $host !== null)
    {
        if(substr_count($host, '.') === 3) $socket_domain = AF_INET;
        elseif(str_contains($host, ':')) $socket_domain = AF_INET6;
    }

    $config = new Config(app: $app,
                         host: $host,
                         port: $port,
                         worker: $worker,
                         socket_type: $socket_type,
                         socket_domain: $socket_domain,
                         socket_backlog: $socket_backlog,
                         socket_protocol: $socket_protocol,
                         socket_read_length: $socket_read_length,
                         timeout_second: $timeout_second,
                         timeout_millisecond: $timeout_millisecond,
                         server: $server,
                         http_protocol: $http_protocol);

    $server = $config->server();
    $server->run();
}

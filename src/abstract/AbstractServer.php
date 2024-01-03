<?php namespace Starutils\Starcorn\abstract;


use Starutils\Starcorn\Config;


abstract class AbstractServer
{
    protected static string $socket_name = "socket";
    protected static string $client_name = "client";
    protected static Config $config;

    public function __construct(Config $config) {
        self::$config = $config;
    }

    public abstract function run(): void;
}
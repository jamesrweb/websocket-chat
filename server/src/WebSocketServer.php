<?php

declare(strict_types=1);

namespace WebSocketChat\Server;

use Exception;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServer
{
    private ?IoServer $io_server = null;

    public function initialise(int $port): self
    {
        if (is_null($this->io_server)) {
            $connection_pool = new ConnectionPool();
            $web_socket_server = new WsServer($connection_pool);
            $http_server = new HttpServer($web_socket_server);

            $this->io_server = IoServer::factory($http_server, $port);
        }

        return $this;
    }

    public function start(): self
    {
        if (is_null($this->io_server)) {
            throw new Exception(get_class($this) . ': Server is not initialised yet.');
        }


        echo 'Listening for requests at ' . $this->io_server->socket->getAddress();
        $this->io_server->run();

        return $this;
    }
}

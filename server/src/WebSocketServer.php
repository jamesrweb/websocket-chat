<?php

declare(strict_types=1);

namespace WebSocketChat\Server;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class WebSocketServer {
    private int $port;
    private LoopInterface $event_loop;
    private ConnectionPool $connection_pool;

    public function __construct(int $port, LoopInterface $event_loop)
    {
        $this->port = $port;
        $this->event_loop = $event_loop;
        $this->connection_pool = new ConnectionPool();
    }

    public function run() {
        $socket_server = new SocketServer("127.0.0.1:$this->port", [], $this->event_loop);
        $socket_server->on('connection', function (ConnectionInterface $connection) {
            $this->connection_pool->addConnection($connection);
        });
    }
}

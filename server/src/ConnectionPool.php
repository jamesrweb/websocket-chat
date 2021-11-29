<?php

declare(strict_types=1);

namespace WebSocketChat\Server;

use Ds\Map;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ConnectionPool implements MessageComponentInterface
{
    private Map $connections;

    public function __construct()
    {
        $this->connections = new Map();
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        $this->connections->put($connection->resourceId, $connection);
    }

    public function onMessage(ConnectionInterface $from, mixed $msg): void
    {
        foreach ($this->connections as $connection) {
            if ($from->resourceId === $connection->resourceId) {
                continue;
            }

            $this->emit("Client $from->resourceId said $msg", $connection);
        }
    }

    public function onClose(ConnectionInterface $connection): void
    {
        if (!$this->connections->hasKey($connection)) {
            return;
        }

        $this->connections->remove($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e): void
    {
        $this->emit('Error: ' . $e->getMessage(), $connection);
    }

    private function emit(mixed $data, ConnectionInterface $connection): void
    {
        $connection->send($data . PHP_EOL);
    }
}

<?php

declare(strict_types=1);

namespace WebSocketChat\Server;

use Ds\Map;
use React\Socket\ConnectionInterface;

class ConnectionPool
{
    private Map $connections;

    public function __construct()
    {
        $this->connections = new Map();
    }

    public function addConnection(ConnectionInterface $connection): void
    {
        $connection->write('Enter your name: ');
        $this->initEvents($connection);
        $this->setConnectionData($connection, []);
    }

    private function initEvents(ConnectionInterface $connection): void
    {
        $connection->on('data', fn (mixed $data) => $this->connectionHandler($connection, $data));
        $connection->on('close', fn () => $this->closeHandler($connection));
    }

    private function connectionHandler(ConnectionInterface $connection, mixed $data): void
    {
        $connectionData = $this->getConnectionData($connection);

        if (is_null($connectionData)) {
            $this->addNewMember($data, $connection);
            return;
        }

        $name = $connectionData['name'];
        $this->emit("$name: $data", $connection);
    }

    private function closeHandler(ConnectionInterface $connection): void
    {
        $data = $this->getConnectionData($connection);
        $name = $data['name'] ?? 'unknown-user';

        $this->connections->remove($connection);
        $this->emit("User $name leaves the chat", $connection);
    }

    private function addNewMember(string $name, ConnectionInterface $connection): void
    {
        $name = str_replace(["\n", "\r"], '', $name);
        $this->setConnectionData($connection, ['name' => $name]);
        $this->emit("User $name joins the chat", $connection);
    }

    /**
     * @param array<string, mixed>  $data
     */
    private function setConnectionData(ConnectionInterface $connection, array $data): void
    {
        $this->connections->put($connection, $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function getConnectionData(ConnectionInterface $connection): ?array
    {
        return $this->connections->get($connection);
    }

    private function emit(mixed $data, ConnectionInterface $current_active_connection): void
    {
        foreach ($this->connections as $connection) {
            if ($connection !== $current_active_connection) {
                $connection->write($data . PHP_EOL);
            }
        }
    }
}

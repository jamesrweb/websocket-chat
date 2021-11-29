<?php

declare(strict_types=1);

namespace WebSocketChat\Server;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$port = $_ENV['PORT'] ?? 8080;

$web_socket_server = new WebSocketServer();
$web_socket_server->initialise($port);
$web_socket_server->start();

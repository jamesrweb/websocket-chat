<?php

declare(strict_types=1);

namespace WebSocketChat\Server;

use React\EventLoop\Loop;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$port = $_ENV['PORT'] ?? 8080;
$event_loop = Loop::get();

$server = new WebSocketServer($port, $event_loop);

$server->run();
$event_loop->run();

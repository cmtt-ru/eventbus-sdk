<?php

require_once 'vendor/autoload.php';

$host = 'localhost';
$port = '5672';
$user = 'rabbitmq';
$password = 'rabbitmqpass';
$vhost = '/';

$amqpConnection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
    $host,
    $port,
    $user,
    $password,
    $vhost
);

$connection = new \Kmtt\EventBusSdk\Connection($amqpConnection);
$slackStream = new \Kmtt\EventBusSdk\Stream\SlackStream($connection);

$slackStream->publish([
    'a' => 1,
    'b' => 2
]);

dd('done!');
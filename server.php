<?php


require_once 'vendor/autoload.php';

use BartoszBartniczak\Ratchet\Websocket\ExchangeRate\ExchangeRates;

$randExchangeRate = rand(40000, 49999) / 10000;

$exchangeRates = new ExchangeRates();

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer($exchangeRates)
    ),
    8080);


$exchangeRates->setLoop($server->loop);
$exchangeRates->setExchangeRate($randExchangeRate);

$server->loop->addPeriodicTimer(3, function () use (&$randExchangeRate, $exchangeRates){
    $change = rand(-15, 15) / 1000;
    $randExchangeRate += $change;
    $exchangeRates->setExchangeRate($randExchangeRate);
});

$server->run();

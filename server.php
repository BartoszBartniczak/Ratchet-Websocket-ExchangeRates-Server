<?php


require_once 'vendor/autoload.php';

use BartoszBartniczak\Ratchet\Websocket\ExchangeRate\ConnectionPoll;
use BartoszBartniczak\Ratchet\Websocket\ExchangeRate\Generator;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

const REFRESH_TIME = 0.2;

$currencies = ['PLN', 'EUR', 'USD', 'CHF'];
$generator = new Generator();
$currentExchangeRates = $generator->generate($currencies);
$exchangeRates = new ConnectionPoll();

$server = IoServer::factory(
    new HttpServer(
        new WsServer($exchangeRates)
    ),
    8080);


$exchangeRates->setLoop($server->loop);
$exchangeRates->setExchangeRates($currentExchangeRates);

$server->loop->addPeriodicTimer(REFRESH_TIME, function () use (&$currentExchangeRates, $exchangeRates, $generator){
    $currentExchangeRates = $generator->generateChanges($currentExchangeRates);
    $exchangeRates->setExchangeRates($currentExchangeRates);
});

$server->run();

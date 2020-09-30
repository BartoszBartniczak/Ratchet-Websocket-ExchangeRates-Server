<?php

namespace BartoszBartniczak\Ratchet\Websocket\ExchangeRate;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use SplObjectStorage;

class ConnectionPoll implements MessageComponentInterface
{

    private SplObjectStorage $connections;

    private LoopInterface $loop;

    /**
     * @var float[]
     */
    private array $exchangeRates;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn)
    {
        $this->sendNewExchangeRate($conn);

        $timer = $this->loop->addPeriodicTimer(3, function () use ($conn) {
            $this->sendNewExchangeRate($conn);
        });

        $this->connections->offsetSet($conn, $timer);

    }

    function onClose(ConnectionInterface $conn)
    {
        $this->loop->cancelTimer($this->connections->offsetGet($conn));
        $this->connections->offsetUnset($conn);
    }

    function onError(ConnectionInterface $conn, Exception $e)
    {
        // TODO: Implement onError() method.
    }

    function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
    }


    public function setLoop($loop)
    {
        $this->loop = $loop;
    }

    public function setExchangeRates(array $exchangeRates)
    {
        $this->exchangeRates = $exchangeRates;
    }

    private function sendNewExchangeRate(ConnectionInterface $conn)
    {

        $conn->send(json_encode(['exchangeRates' => $this->formatExchangeRates()]));
    }

    /**
     * @return array|string[]
     */
    private function formatExchangeRates()
    {
        return array_map(function (array $exchangeRates) {
            return [
                'buy'=>number_format($exchangeRates['buy'], 4, '.', ''),
                'sell'=>number_format($exchangeRates['sell'], 4, '.', '')
            ];
        }, $this->exchangeRates);
    }


}

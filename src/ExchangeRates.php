<?php

namespace BartoszBartniczak\Ratchet\Websocket\ExchangeRate;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use SplObjectStorage;

class ExchangeRates implements MessageComponentInterface
{

    /**
     * @var SplObjectStorage
     */
    private $connections;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var float
     */
    private $exchangeRate;

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

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    protected function sendNewExchangeRate(ConnectionInterface $conn)
    {
        $conn->send(json_encode(['exchangeRate' => number_format($this->exchangeRate, 4, '.', '')]));
    }


}

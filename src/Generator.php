<?php


namespace BartoszBartniczak\Ratchet\Websocket\ExchangeRate;


class Generator
{

    /**
     * @return float[]
     */
    public function generate(array $currencies): array{
        $exchangeRates = [];

        foreach ($currencies as $currency){
            $buy = $this->generateBuy();
            $exchangeRates[$currency]['buy'] = $buy;
            $exchangeRates[$currency]['sell'] = $this->generateSell($buy);
        }

        return $exchangeRates;
    }

    /**
     * @param float[] $currentExchangeRates
     * @return float[]
     */
    public function generateChanges(array $currentExchangeRates):array
    {
        $newExchangeRates = [];

        foreach ($currentExchangeRates as $currency => $currentExchangeRate){
            $newBuy = $this->generateBuyChange($currentExchangeRate['buy']);
            $newExchangeRates[$currency]['buy'] = $newBuy;
            $newExchangeRates[$currency]['sell'] = $this->generateSell($newBuy);
        }

        return $newExchangeRates;
    }

    private function generateBuyChange(float $exchangeRate):float{
        return $exchangeRate + (rand(-15, 15) / 1000);
    }

    private function generateBuy():float
    {
        return rand(40000, 49999) / 10000;
    }

    private function generateSell(float $buy):float
    {
        return $buy - rand(10, 20)/1000;
    }

}

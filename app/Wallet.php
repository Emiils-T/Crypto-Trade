<?php

namespace App;

use Carbon\Carbon;
use JsonSerializable;

class Wallet implements JsonSerializable
{
    private string $name;
    private string $symbol;
    private float $amount;
    private float $price;
    private Carbon $dateOfPurchase;
    private float $value;
    private int $purchasePrice;
    private float $valueNow;
    private ?float $profit;

    public function __construct(string $name,
                                string $symbol,
                                float  $amount,
                                float  $price,
                                int    $purchasePrice,
                                Carbon $dateOfPurchase,
                                float  $value,
                                float  $valueNow,
                                ?float $profit = 0)
    {

        $this->name = $name;
        $this->symbol = $symbol;
        $this->amount = $amount;
        $this->price = $price;
        $this->dateOfPurchase = $dateOfPurchase;
        $this->value = $value;
        $this->purchasePrice = $purchasePrice;
        $this->valueNow = $valueNow;
        $this->profit = $profit;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDateOfPurchase(): Carbon
    {
        return $this->dateOfPurchase;
    }

    public function getPurchasePrice(): float
    {
        return $this->purchasePrice;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValueNow(float $valueNow): void
    {
        $this->valueNow = $valueNow;
    }

    public function getValueNow(): float
    {
        return $this->valueNow;
    }

    public function getProfit(): ?float
    {
        return $this->profit;
    }

    public function setProfit(?float $profit): void
    {
        $this->profit = $profit;
    }


    public function jsonSerialize(): array
    {
        return [
            "name" => $this->name,
            "symbol" => $this->symbol,
            "amount" => $this->amount,
            "price" => $this->price,
            "purchasePrice" => $this->purchasePrice,
            "dateOfPurchase" => $this->dateOfPurchase,
            "value" => $this->value,
            "valueNow" => $this->valueNow,
            "profit" => $this->profit,
        ];
    }
}
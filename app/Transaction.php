<?php


namespace App;

use Carbon\Carbon;
use JsonSerializable;

class Transaction implements JsonSerializable
{
    private Carbon $date;
    private string $transaction;

    public function __construct(string $transaction, Carbon $date)
    {
        $this->date = $date;
        $this->transaction = $transaction;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTransaction(): string
    {
        return $this->transaction;
    }

    function jsonSerialize(): array
    {
        return [
            'transaction' => $this->transaction,
            'date' => $this->date,
        ];
    }
}
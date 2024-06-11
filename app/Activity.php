<?php

namespace App;

use Carbon\Carbon;
use JsonSerializable;

class Activity implements JsonSerializable
{
    private string $activity;
    private Carbon $date;

    public function __construct(string $activity, Carbon $date)
    {
        $this->activity = $activity;
        $this->date = $date;
    }

    public function getDate(): string
    {
        return $this->date->toDateTimeString();
    }

    public function getActivity(): string
    {
        return $this->activity;
    }

    function jsonSerialize(): array
    {
        return [
            'activity' => $this->activity,
            'date' => $this->date->toDateTimeString()
        ];
    }
}
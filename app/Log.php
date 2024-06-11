<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Carbon\Carbon;

class Log
{
    private array $activity;
    private string $baseDir;

    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
        $this->activity = $this->getLog();
    }

    public function addActivityToLog(Activity $activity): void
    {
        $this->activity[] = $activity;
        $this->saveToLog();
    }

    public function saveToLog(): void
    {
        $filePath = $this->baseDir . "/log/log.json";
        $jsonData = json_encode($this->activity, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }

    public function getLog(): array
    {
        $filePath = $this->baseDir . "/log/log.json";
        if (!file_exists($filePath)) {
            return [];
        }
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        $log = [];

        foreach ($data as $item) {
            if (isset($item['activity']) && isset($item['date'])) {
                $log[] = new Activity(
                    $item['activity'],
                    Carbon::parse($item['date'])->setTimezone('Europe/Riga')
                );
            }
        }
        return $log;
    }

    public function displayLog(): void
    {
        $rows = [];
        foreach ($this->activity as $index => $item) {
            $nameCell = $item->getActivity();
            $dateCell = $item->getDate();

            $rows[] = [
                $index,
                $nameCell,
                $dateCell
            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders(["Index", "Activity", "Date"])
            ->setRows($rows);
        $table->render();
    }
}

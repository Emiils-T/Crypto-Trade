<?php


namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Carbon\Carbon;

class TransactionLog
{
    private string $baseDir;
    private array $transaction;

    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
        $this->transaction = $this->getTransactionLogs();
    }

    public function addTransactionToLog(Transaction $transaction): void
    {
        $this->transaction[] = $transaction;
        $this->saveToTransactionLog();
    }

    public function saveToTransactionLog(): void
    {
        $filePath = $this->baseDir . "/transactions/transactionLog.json";
        $jsonData = json_encode($this->transaction, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }

    public function getTransactionLogs(): array
    {
        $filePath = $this->baseDir . "/transactions/transactionLog.json";
        if (!file_exists($filePath)) {
            return [];
        }
        $jasonData = file_get_contents($filePath);
        $data = json_decode($jasonData, true);


        $log = [];

        foreach ($data as $item) {
            $log[] = new Transaction(
                $item['transaction'],
                Carbon::parse($item['date'])->setTimezone('Europe/Riga')
            );
        }
        return $log;
    }

    public function displayTransactionLog(): void
    {
        $rows = [];
        foreach ($this->transaction as $index => $item) {
            $nameCell = $item->getTransaction();
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
            ->setHeaders(["Index", "Transaction", "Date"])
            ->setRows($rows);
        $table->render();
    }
}
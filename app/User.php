<?php

namespace App;

use JsonSerializable;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class User implements JsonSerializable
{
    private string $name;
    private float $wallet;
    private string $baseDir;

    public function __construct(string $name, float $wallet, string $baseDir)
    {
        $this->name = $name;
        $this->wallet = $wallet;
        $this->baseDir = $baseDir;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWallet(): float
    {
        return $this->wallet;
    }

    public function setWallet(float $wallet): void
    {
        $this->wallet = $wallet;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'wallet' => $this->wallet,
        ];
    }

    public function saveToFile(): void
    {
        $filePath = $this->baseDir . "/User/UserWallet.json";
        $jsonData = json_encode($this, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }

    public static function loadFromFile(string $baseDir): ?User
    {
        $filePath = $baseDir . "/User/UserWallet.json";
        if (!file_exists($filePath)) {
            return null;
        }

        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);
        return new User($data['name'], $data['wallet'], $baseDir);
    }

    public function displayUser(): void
    {
        $rows = [];
        $rows[] = [
            $this->getName(),
            $this->getWallet()];

        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders([
                "User name",
                "Wallet $",
            ])
            ->setRows($rows);
        $table->render();
    }

}
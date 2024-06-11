<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Carbon\Carbon;
use App\Wallet;


class Exchange
{
    private array $crypto;
    private string $baseDir;
    private array $wallet;
    private User $user;


    public function __construct(string $baseDir, User $user)
    {
        $this->crypto = $this->getCryptoList();
        $this->baseDir = $baseDir;
        $this->wallet = $this->getWallet();
        $this->user = $user;

    }

    public function getCryptoList(): array
    {
        $apiKey = '38b5142f-52e3-4a86-935c-5f63dd67cc34';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accepts: application/json",
                "X-CMC_PRO_API_KEY: $apiKey",
            ],
        ]);
        $data = [];
        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            echo "cURL Error #:" . $error;
        } else {
            $data = json_decode($response, true);
        }
        return $data;
    }


    public function selectCrypto(int $index)
    {
        $cryptoList = $this->getCryptoList();

        return $cryptoList['data'][$index];
    }

    public function getWallet(): array
    {
        $filePath = $this->baseDir . "/Wallet/Wallet.json";
        if (!file_exists($filePath)) {
            return [];
        }
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        $items = [];
        foreach ($data as $value) {
            $items[] = new Wallet(
                $value['name'],
                $value['symbol'],
                $value['amount'],
                $value['price'],
                $value['purchasePrice'],
                Carbon::parse($value['dateOfPurchase'])->setTimezone('Europe/Riga'),
                $value['value'],
                $value["valueNow"]);
        }
        return $items;
    }


    public function addToWallet(Wallet $coin): void
    {
        $this->wallet[] = $coin;
        $this->saveToWallet();
    }


    public function saveToWallet(): void
    {
        $filePath = $this->baseDir . "/Wallet/Wallet.json";
        $jsonData = json_encode($this->wallet, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }


    public function sell(int $index)//TODO:getWallet() un user Wallet PLus
    {

        $this->user->setWallet($this->user->getWallet() + $this->wallet[$index]->getValueNow());

        if (isset($this->wallet[$index])) {
            unset($this->wallet[$index]);
            $this->wallet = array_values($this->wallet);
            $this->saveToWallet();
        } else {
            echo "ERROR: Invalid input\n";
        }
    }

    public function updateWallet(): void//Todo:insert in displayWallet() to be automatically called.
    {
        $wallet = $this->getWallet();
        foreach ($wallet as $crypto) {
            $search = $this->search($crypto->getSymbol());
            $valueNow = $search['quote']['USD']['price'] * $crypto->getAmount();
            $crypto->setValueNow($valueNow);
            $crypto->setProfit(($crypto->getValueNow()) - ($crypto->getValue()));
        }
        $this->wallet = $wallet;
        $this->saveToWallet();
    }

    public function searchAndDisplay(string $symbol)
    {
        $selectedCrypto = null;

        foreach ($this->crypto['data'] as $key => $crypto) {

            if ($crypto['symbol'] === $symbol) {
                $selectedCrypto = $this->crypto['data'][$key];
            }
        }
        if ($selectedCrypto == null) {
            echo "Error : couldn't find crypto $symbol";
            return null;
        }

        $rows = [];
        $rows[] = [
            $selectedCrypto['name'],
            $selectedCrypto['symbol'],
            $selectedCrypto['quote']['USD']['price'],
            $selectedCrypto['quote']['USD']['market_cap'],
            $selectedCrypto['quote']['USD']['volume_24h']
        ];

        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders([
                "Name",
                "Symbol",
                "Price",
                "Marker Cap",
                "Volume 24h",
            ])
            ->setRows($rows);
        $table->render();
    }

    public function search(string $symbol): ?array
    {
        $selectedCrypto = null;
        foreach ($this->crypto['data'] as $key => $crypto) {

            if ($crypto['symbol'] === $symbol) {
                $selectedCrypto = $this->crypto['data'][$key];
            }
        }
        if ($selectedCrypto == null) {
            echo "Error : couldn't find crypto $symbol\n";
            return null;
        } else {
            return $selectedCrypto;
        }
    }

    public function displayCrypto(): void
    {
        $rows = [];
        foreach ($this->crypto['data'] as $index => $crypto) {
            $rows[] = [
                $index,
                $crypto['name'],
                $crypto['symbol'],
                $crypto['quote']['USD']['price'],

            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table
            ->setHeaders([
                "Index",
                "Name",
                "Symbol",
                "Price"
            ])
            ->setRows($rows);
        $table->render();
    }

    public function displayWallet(): void
    {
        $this->updateWallet();
        $rows = [];
        foreach ($this->wallet as $index => $wallet) {
            $rows[] = [
                $index,
                $wallet->getName(),
                $wallet->getSymbol(),
                $wallet->getPrice(),
                $wallet->getPurchasePrice(),
                $wallet->getAmount(),
                $wallet->getDateOfPurchase(),
                $wallet->getValue(),
                $wallet->getValueNow(),
                $wallet->getProfit()
            ];
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table->setHeaders([
            "Index",
            "Name",
            "Symbol",
            "Price",
            "Purchase Price",
            "Amount",
            "DateOfPurchase",
            "Value",
            "Value Now",
            "Profit/Loss"]);
        $table->setRows($rows);
        $table->render();
    }
}
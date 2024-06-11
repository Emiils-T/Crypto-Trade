<?php

require_once './vendor/autoload.php';

use App\Exchange;
use App\Wallet;
use App\User;
use App\Log;
use App\Transaction;
use App\TransactionLog;
use App\Activity;
use Carbon\Carbon;


$baseDir = __DIR__;
$userFilePath = __DIR__ . "/User/UserWallet.json";
$user = User::loadFromFile($baseDir);
if ($user === null) {
    $name = (string)readline("Enter user name: ");
    $walletAmount = (float)readline("Enter wallet amount: ");
    $user = new User($name, $walletAmount, $baseDir);
    $user->saveToFile();
}

while (true) {
    $user->displayUser();
    echo "1. List top crypto\n2. Search for crypto by Symbol\n3. Buy crypto\n4. Sell crypto\n";
    echo "5. Display Wallet\n6. Display activity log\n7. Transaction List\n8. Exit\n";
    $choice = (int)readline("Enter index to select choice: ");

    switch ($choice) {
        case 1:
            $exchange = new Exchange($baseDir, $user);
            $exchange->displayCrypto();
            break;
        case 2:
            $symbol = strtoupper((string)readline("Enter symbol: "));
            $exchange = new Exchange($baseDir, $user);
            $exchange->searchAndDisplay($symbol);

            $activity = new Activity("{$user->getName()}:Searched for $symbol", carbon::now());
            $log = new Log($baseDir);
            $log->addActivityToLog($activity);
            break;
        case 3:
            $exchange = new Exchange($baseDir, $user);
            $exchange->displayCrypto();
            $index = (int)readline("Enter index to select Crypto: ");
            $selectedCrypto = $exchange->selectCrypto($index);
            $name = $selectedCrypto['name'];
            $symbol = $selectedCrypto['symbol'];
            $price = $selectedCrypto['quote']['USD']['price'];
            $purchasePrice = (int)readline("Enter how much to buy in USD: ");
            $amount = $purchasePrice / $price;//dollars worth
            $dateOfPurchase = Carbon::now('Europe/Riga');
            $value = $amount * $price;
            $valueNow = $amount * $price;
            $user->setWallet($user->getWallet() - $purchasePrice);

            $crypto = new Wallet($name, $symbol, $amount, $price, $purchasePrice, $dateOfPurchase, $value, $valueNow);
            $exchange->addToWallet($crypto);
            $user->saveToFile();

            $transaction = new Transaction("Bought $symbol for $$purchasePrice", carbon::now());
            $transactionLog = new TransactionLog($baseDir);
            $transactionLog->addTransactionToLog($transaction);

            $activity = new Activity("{$user->getName()}:Bought $symbol for $$purchasePrice", carbon::now());
            $log = new Log($baseDir);
            $log->addActivityToLog($activity);
            break;
        case 4:
            $exchange = new Exchange($baseDir, $user);
            $exchange->displayWallet();
            $index = (int)readline("Enter index to select Crypto: ");
            $cryptoName = $exchange->getWallet()[$index]->getName();
            $exchange->sell($index);
            $user->saveToFile();


            $transaction = new Transaction("Sold $cryptoName", carbon::now());
            $transactionLog = new TransactionLog($baseDir);
            $transactionLog->addTransactionToLog($transaction);

            $activity = new Activity("{$user->getName()}:Sold $cryptoName", carbon::now());
            $log = new Log($baseDir);
            $log->addActivityToLog($activity);
            break;
        case 5:
            $exchange = new Exchange($baseDir, $user);
            $exchange->displayWallet();
            $activity = new Activity("{$user->getName()}:Looked at wallet", carbon::now());
            $log = new Log($baseDir);
            $log->addActivityToLog($activity);
            break;
        case 6:
            $log = new Log($baseDir);
            $log->displayLog();
            break;
        case 7:
            $transactionLog = new TransactionLog($baseDir);
            $transactionLog->displayTransactionLog();
            break;
        case 8:
            $user->saveToFile();
            exit;
        default:
            echo "Error: Wrong Input";
            break;
    }
}
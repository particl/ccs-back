<?php

namespace App\Coin;

use Illuminate\Console\Command;

use Monero\WalletCommon;

interface Coin
{
    public function newWallet() : WalletCommon;
    public function onNotifyGetTransactions(Command $command, WalletCommon $wallet);
    public function subaddrIndex($addressDetails, $project);
}

class CoinAuto
{
    public static function newCoin() : Coin
    {
        $coin = env('COIN', 'particl');
        switch ($coin) {
            case 'particl':
                return new CoinParticl();
            default:
                throw new \Exception('Unsupported COIN ' . $coin);
        }
    }
}

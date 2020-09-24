<?php

namespace App\Coin;

use App\Deposit;
use App\Project;
use Illuminate\Console\Command;

use Monero\WalletCommon;
use Monero\WalletParticl;

class CoinParticl implements Coin
{
    public function newWallet() : WalletCommon
    {
        return new WalletParticl();
    }

    public function onNotifyGetTransactions(Command $command, WalletCommon $wallet)
    {
        return $wallet->scanIncomingTransfers()->each(function ($tx) {
            $project = Project::where('address', $tx->address)->first();
            if ($project) {
                $tx->subaddr_index = $project->subaddr_index;
            }
        });
    }

    public function subaddrIndex($addressDetails, $project)
    {
        return $project->id;
    }
}

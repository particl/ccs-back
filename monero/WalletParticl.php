<?php

namespace Monero;

use Carbon\Carbon;

class WalletParticl implements WalletCommon
{
    private $rpc;

    public static function digitsAfterTheRadixPoint() : int
    {
        return 8;
    }

    public function __construct()
    {
        $this->rpc = new jsonRpcBase([  'auth_type' => 'basic',
                                        'username' => env('RPC_USER'),
                                        'password' => env('RPC_PASSWORD'),
                                        'url' => env('RPC_URL')]);
    }

    public function getPaymentAddress()
    {
        return ['address' => $this->rpc->request('getnewaddress')];
    }

    private function decodeTxAmount(string $tx_amount) : int
    {
        $tx_amount = str_replace(',', '.',  $tx_amount);

        $amount = explode('.', $tx_amount);
        if (sizeof($amount) < 1 || sizeof($amount) > 2) {
            throw new \Exception('Failed to decode tx amount ' . $tx_amount);
        }

        $fraction = $amount[1] ?? "";
        if (strlen($fraction) > $this->digitsAfterTheRadixPoint()) {
            throw new \Exception('Failed to decode tx amount, too many digits after the redix point ' . $tx_amount);
        }

        $amount = $amount[0] . str_pad($fraction, $this->digitsAfterTheRadixPoint(), '0');
        $amount = intval($amount);
        if ($amount == 0) {
            throw new \Exception('Failed to convert tx amount to int ' . $tx_amount);
        }

        return $amount;
    }

    public function scanIncomingTransfers($skip_txes = 0)
    {
        return collect($this->rpc->request('listtransactions', ['*', 100, $skip_txes]))->filter(function ($tx) {
            return isset($tx['address']) && $tx['category'] == 'receive';
        })->map(function ($tx) {
            return new Transaction(
                $tx['txid'],
                $this->decodeTxAmount($tx['amount']),
                $tx['address'],
                $tx['confirmations'],
                0,
                Carbon::now(),
                0,
                isset($tx['blockhash']) ? $this->blockHeightByHash($tx['blockhash']) : 0
            );
        });
    }

    public function checkIncomingVotes($vote_id, $block_start, $block_end)
    {
        $msg = gettype($vote_id) . " " . gettype($block_start) . " " . gettype($block_end);
        error_log($msg, 0);
        $vote = $this->rpc->request('tallyvotes', [(int) $vote_id, (int) $block_start, (int) $block_end]);

        // Split string "y, x%" and return y
        function extract_votes($votes_str = null) {
            // Check if the value is set and not null
            if(isset($votes_str)) {
                $votes_splitted = explode(',', $votes_str);
                return intval($votes_splitted[0]);
            } else {
                return 0;
            }
        }

        $votes_option_yes = extract_votes($vote['Option 1']);
        $votes_option_no = extract_votes($vote['Option 2']);
        $votes_option_abstain = extract_votes($vote['Abstain']);

        return new VotingState(
            $vote['proposal'], /* current proposal id */
            $block_start,
            $block_end,
            $vote['blocks_counted'],
            $votes_option_yes,
            $votes_option_no,
            $votes_option_abstain
        );

    }

    public function blockHeight() : int
    {
        return $this->rpc->request('getblockcount');
    }

    public function createQrCodeString($address, $amount = null) : string
    {
        return 'particl:' . $address . ($amount ? '?amount=' . $amount : '');
    }

    private function blockHeightByHash($block_hash) : int
    {
        return $this->rpc->request('getblockheader', [$block_hash])['height'];
    }
}

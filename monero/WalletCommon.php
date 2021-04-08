<?php

namespace Monero;

interface WalletCommon
{
    public static function digitsAfterTheRadixPoint() : int;
    public function getPaymentAddress();
    public function scanIncomingTransfers($min_height = 0);
    public function checkIncomingVotes($vote_id, $block_start, $block_end);
    public function blockHeight() : int;
    public function createQrCodeString($address, $amount = null) : string;
}

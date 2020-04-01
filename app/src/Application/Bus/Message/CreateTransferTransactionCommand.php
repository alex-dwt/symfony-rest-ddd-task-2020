<?php

declare(strict_types=1);

namespace App\Application\Bus\Message;

use App\Domain\Wallet\Wallet;

class CreateTransferTransactionCommand
{
    private Wallet $senderWallet;
    private Wallet $recipientWallet;
    private float $amount;

    public function __construct(
        Wallet $senderWallet,
        Wallet $recipientWallet,
        float $amount
    ) {
        $this->senderWallet = $senderWallet;
        $this->recipientWallet = $recipientWallet;
        $this->amount = $amount;
    }

    public function senderWallet(): Wallet
    {
        return $this->senderWallet;
    }

    public function recipientWallet(): Wallet
    {
        return $this->recipientWallet;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}

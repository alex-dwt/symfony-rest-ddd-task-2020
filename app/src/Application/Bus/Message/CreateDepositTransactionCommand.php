<?php

declare(strict_types=1);

namespace App\Application\Bus\Message;

use App\Domain\Wallet\Wallet;

class CreateDepositTransactionCommand
{
    private Wallet $wallet;
    private float $amount;

    public function __construct(
        Wallet $wallet,
        float $amount
    ) {
        $this->wallet = $wallet;
        $this->amount = $amount;
    }

    public function wallet(): Wallet
    {
        return $this->wallet;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}

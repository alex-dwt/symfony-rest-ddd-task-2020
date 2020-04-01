<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Wallet\Wallet;

class WalletRepository extends AbstractDoctrineRepository
{
    public function repositoryClassName(): string
    {
        return Wallet::class;
    }
}

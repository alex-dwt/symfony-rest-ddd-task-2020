<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Wallet\Transaction;

class TransactionRepository extends AbstractDoctrineRepository
{
    public function repositoryClassName(): string
    {
        return Transaction::class;
    }
}

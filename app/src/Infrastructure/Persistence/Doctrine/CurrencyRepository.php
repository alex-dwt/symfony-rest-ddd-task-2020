<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Wallet\Currency;

class CurrencyRepository extends AbstractDoctrineRepository
{
    public function repositoryClassName(): string
    {
        return Currency::class;
    }
}

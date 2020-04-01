<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Wallet\ExchangeRate;

class ExchangeRateRepository extends AbstractDoctrineRepository
{
    public function repositoryClassName(): string
    {
        return ExchangeRate::class;
    }
}

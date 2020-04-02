<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Common\DomainCriteria;
use App\Domain\Wallet\ExchangeRate;

/**
 * @method ExchangeRate|null getOneByCriteria(DomainCriteria ...$criterias)
 */
class ExchangeRateRepository extends AbstractDoctrineRepository
{
    public function repositoryClassName(): string
    {
        return ExchangeRate::class;
    }
}

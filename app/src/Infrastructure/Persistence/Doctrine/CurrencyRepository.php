<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Common\DomainCriteria;
use App\Domain\Wallet\Currency;

/**
 * @method Currency|null getOneByCriteria(DomainCriteria ...$criterias)
 * @method Currency[] getAll()
 */
class CurrencyRepository extends AbstractDoctrineRepository
{
    public function repositoryClassName(): string
    {
        return Currency::class;
    }
}

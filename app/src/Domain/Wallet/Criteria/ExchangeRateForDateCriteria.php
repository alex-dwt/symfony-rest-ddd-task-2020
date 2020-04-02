<?php

declare(strict_types=1);

namespace App\Domain\Employee\Criteria;

use App\Domain\Common\DomainCriteria;
use App\Domain\Wallet\Currency;
use Doctrine\Common\Collections\Criteria;

class ExchangeRateForDateCriteria implements DomainCriteria
{
    private \DateTimeInterface $date;
    private Currency $currencyFrom;
    private Currency $currencyTo;

    public function __construct(
        \DateTimeInterface $date,
        Currency $currencyFrom,
        Currency $currencyTo
    ) {
        $this->date = $date;
        $this->currencyFrom = $currencyFrom;
        $this->currencyTo = $currencyTo;
    }

    public function create(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('currencyFrom', $this->currencyFrom))
            ->andWhere(Criteria::expr()->eq('currencyTo', $this->currencyTo))
            ->andWhere(Criteria::expr()->eq('date', $this->date->format('Y-m-d')));
    }
}

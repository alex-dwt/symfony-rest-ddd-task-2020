<?php

declare(strict_types=1);

namespace App\Domain\Employee\Criteria;

use App\Domain\Common\DomainCriteria;
use Doctrine\Common\Collections\Criteria;

class CurrencyByNameCriteria implements DomainCriteria
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = strtolower($name);
    }

    public function create(): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('name', $this->name));
    }
}

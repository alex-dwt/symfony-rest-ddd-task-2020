<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Wallet\Criteria\ExchangeRateForDateCriteria;
use App\Domain\Wallet\Currency;
use App\Infrastructure\Persistence\Doctrine\CurrencyRepository;
use App\Infrastructure\Persistence\Doctrine\ExchangeRateRepository;

class CurrencyConverter
{
    private ExchangeRateRepository $exchangeRateRepository;
    private CurrencyRepository $currencyRepository;

    public function __construct(
        ExchangeRateRepository $exchangeRateRepository,
        CurrencyRepository $currencyRepository
    ) {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function execute(
        Currency $currencyFrom,
        Currency $currencyTo,
        float $amount,
        ?\DateTimeInterface $date = null
    ): float {
       if ($currencyFrom === $currencyTo) {
           return $amount;
       }

        $date = $date ?? new \DateTimeImmutable();

        $rate = $this
            ->exchangeRateRepository
            ->getOneByCriteria(
                new ExchangeRateForDateCriteria(
                    $date ?? new \DateTimeImmutable(),
                    $currencyFrom,
                    $currencyTo
                )
            );

        if (!$rate) {
            throw new \RuntimeException('Exchange rate is not found, please run parser to get rates');
        }

        $amount *= $rate->getRate();

        return (float) bcdiv((string) $amount, '1', 2);
    }
}

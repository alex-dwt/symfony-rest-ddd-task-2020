<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Wallet\ExchangeRate;
use App\Infrastructure\Persistence\Doctrine\CurrencyRepository;
use App\Infrastructure\Persistence\Doctrine\ExchangeRateRepository;

class CurrenciesRatesFetcher
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

    public function execute()
    {
        $date = new \DateTimeImmutable();

        foreach ($this->currencyRepository->getAll() as $currency) {
            if ($currency->isUsd()) {
                continue;
            }

            $exchangeRate = new ExchangeRate(
                $date,
                $currency,
                $this->getUsdRate($currency->getName())
            );

            $this->exchangeRateRepository->add($exchangeRate);
        }
    }

    /**
     * TODO Rewrite to something useful
     */
    private function getUsdRate(string $currency): int
    {
        if ($currency === 'gbp') {
            return 124;
        } elseif ($currency === 'eur') {
            return 109;
        } elseif ($currency === 'pln') {
            return 24;
        } else {
            throw new \LogicException('Not implemented');
        }
    }
}

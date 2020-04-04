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

    public function execute(\DateTimeImmutable $date)
    {
        $currencies = $this->currencyRepository->getAll();

        foreach ($currencies as $currencyFrom) {
            foreach ($currencies as $currencyTo) {
                if ($currencyFrom === $currencyTo) {
                    continue;
                }

                $exchangeRate = new ExchangeRate(
                    $date,
                    $currencyFrom,
                    $currencyTo,
                    $this->getRate($currencyFrom->getName(), $currencyTo->getName())
                );

                $this->exchangeRateRepository->add($exchangeRate);
            }
        }
    }

    /**
     * TODO Rewrite to something useful
     */
    private function getRate(string $currencyFrom, string $currencyTo): float
    {
        $fakeRatesTable = $this->getFakeRatesTable();

        $key = "$currencyFrom:$currencyTo";

        if (!isset($fakeRatesTable[$key])) {
            throw new \LogicException('This should not have happened');
        }

        return $fakeRatesTable[$key];
    }

    private function getFakeRatesTable(): array
    {
        return [
            'gbp:usd' => 1.24 + rand(1, 5) / 100,
            'gbp:eur' => 1.13 + rand(1, 5) / 100,
            'gbp:pln' => 5.21 + rand(1, 5) / 100,

            'usd:gbp' => 0.81 + rand(1, 5) / 100,
            'usd:eur' => 0.91 + rand(1, 5) / 100,
            'usd:pln' => 4.20 + rand(1, 5) / 100,

            'eur:usd' => 1.10 + rand(1, 5) / 100,
            'eur:gbp' => 0.88 + rand(1, 5) / 100,
            'eur:pln' => 4.60 + rand(1, 5) / 100,

            'pln:usd' => 0.24 + rand(1, 5) / 100,
            'pln:eur' => 0.22 + rand(1, 5) / 100,
            'pln:gbp' => 0.19 + rand(1, 5) / 100,
        ];
    }
}

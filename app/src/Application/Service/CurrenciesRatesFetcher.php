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

    private const RATES = [
        'gbp:usd' => 1.24,
        'gbp:eur' => 1.13,
        'gbp:pln' => 5.21,

        'usd:gbp' => 0.81,
        'usd:eur' => 0.91,
        'usd:pln' => 4.20,

        'eur:usd' => 1.10,
        'eur:gbp' => 0.88,
        'eur:pln' => 4.60,

        'pln:usd' => 0.24,
        'pln:eur' => 0.22,
        'pln:gbp' => 0.19,
    ];

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
        $key = "$currencyFrom:$currencyTo";

        if (!isset(self::RATES[$key])) {
            throw new \LogicException('This should not have happened');
        }

        return self::RATES[$key];
    }
}

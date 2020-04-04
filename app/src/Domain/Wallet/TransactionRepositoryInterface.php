<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Common\DomainRepository;

interface TransactionRepositoryInterface extends DomainRepository
{
    public function getBalance(Wallet $wallet): float;

    public function getSummaryForPeriod(
        Wallet $wallet,
        ?\DateTimeImmutable $dateFrom,
        ?\DateTimeImmutable $dateTill,
        int $usdCurrencyId
    ): array;

    public function getTransactionsForPeriod(
        Wallet $wallet,
        ?\DateTimeImmutable $dateFrom,
        ?\DateTimeImmutable $dateTill,
        int $usdCurrencyId
    ): array;
}

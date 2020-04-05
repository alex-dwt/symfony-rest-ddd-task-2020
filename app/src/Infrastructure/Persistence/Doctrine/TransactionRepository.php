<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Wallet\TransactionRepositoryInterface;
use App\Domain\Wallet\Transaction;
use App\Domain\Wallet\Wallet;
use Doctrine\DBAL\FetchMode;

class TransactionRepository extends AbstractDoctrineRepository implements TransactionRepositoryInterface
{
    public function repositoryClassName(): string
    {
        return Transaction::class;
    }

    public function getBalance(Wallet $wallet, bool $setLock = false): float
    {
        $conn = $this->em->getConnection();

        $sql = '
            SELECT SUM(' . $this->getAmountFieldSelectSql() . ') as balance
            FROM transactions
            WHERE
                sender_wallet_id = :walletId
                OR recipient_wallet_id = :walletId
        ';

        if ($setLock) {
            $sql .= ' FOR UPDATE';
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'depositType' => Transaction::TYPE_DEPOSIT,
            'transferType' => Transaction::TYPE_TRANSFER,
            'walletId' => $wallet->getId(),
        ]);

        return (float) $stmt->fetchColumn();
    }

    public function getSummaryForPeriod(
        Wallet $wallet,
        ?\DateTimeImmutable $dateFrom,
        ?\DateTimeImmutable $dateTill,
        int $usdCurrencyId
    ): array {
        $conn = $this->em->getConnection();

        $sql = '
            SELECT
                IFNULL(SUM(' . $this->getAmountFieldSelectSql() . '), 0) as amount,
                IFNULL(SUM(' . $this->getAmountUsdFieldSelectSql() . '), 0) as usdAmount
            FROM transactions ' . $this->getJoinsPeriodSqlPart() . '
            WHERE
                (
                    sender_wallet_id = :walletId
                    OR recipient_wallet_id = :walletId
                )
            ';

        $periodConditionSql = $this->getPeriodConditionSql($dateFrom, $dateTill);
        if ($periodConditionSql) {
            $sql .= ' AND ' . $periodConditionSql;
        }

        $params = [
            'depositType' => Transaction::TYPE_DEPOSIT,
            'transferType' => Transaction::TYPE_TRANSFER,
            'walletId' => $wallet->getId(),
            'usdCurrencyId' => $usdCurrencyId,
        ];

        if ($dateFrom) {
            $params += ['dateFrom' => $dateFrom->format('Y-m-d H:i:s')];
        }
        if ($dateTill) {
            $params += ['dateTill' => $dateTill->format('Y-m-d H:i:s')];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(FetchMode::ASSOCIATIVE)[0];
    }

    public function getTransactionsForPeriod(
        Wallet $wallet,
        ?\DateTimeImmutable $dateFrom,
        ?\DateTimeImmutable $dateTill,
        int $usdCurrencyId
    ): array {
        $conn = $this->em->getConnection();

        $sql = '
            SELECT
                transactions.id,
                transactions.type,
                transactions.date_time,
                CASE
                    WHEN (type = :depositType OR type = :transferType) AND recipient_wallet_id = :walletId
                        THEN recipient_currencies.name
                    WHEN type = :transferType AND sender_wallet_id = :walletId
                        THEN sender_currencies.name
                    ELSE NULL
                END as currency,
                ' . $this->getAmountFieldSelectSql() . ' as amount,
                ' . $this->getAmountUsdFieldSelectSql() . ' as usdAmount,
                CASE
                    WHEN (type = :depositType OR type = :transferType) AND recipient_wallet_id = :walletId
                        THEN IFNULL(er1.rate, 1)
                    WHEN type = :transferType AND sender_wallet_id = :walletId
                        THEN IFNULL(er2.rate, 1)
                    ELSE NULL
                END as usdRate
            FROM transactions ' . $this->getJoinsPeriodSqlPart() . '
            WHERE
                (
                    sender_wallet_id = :walletId
                    OR recipient_wallet_id = :walletId
                )
            ';

        $periodConditionSql = $this->getPeriodConditionSql($dateFrom, $dateTill);
        if ($periodConditionSql) {
            $sql .= ' AND ' . $periodConditionSql;
        }

        $sql .= ' ORDER BY date_time';

        $params = [
            'depositType' => Transaction::TYPE_DEPOSIT,
            'transferType' => Transaction::TYPE_TRANSFER,
            'walletId' => $wallet->getId(),
            'usdCurrencyId' => $usdCurrencyId,
        ];

        if ($dateFrom) {
            $params += ['dateFrom' => $dateFrom->format('Y-m-d H:i:s')];
        }
        if ($dateTill) {
            $params += ['dateTill' => $dateTill->format('Y-m-d H:i:s')];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }

    private function getPeriodConditionSql(?\DateTimeImmutable $dateFrom, ?\DateTimeImmutable $dateTill): string
    {
        $sql = '';

        if ($dateFrom) {
            $sql .= ' transactions.date_time >= :dateFrom ';
        }

        if ($dateTill) {
            $sql .= ($sql ? ' AND ' : '') . ' transactions.date_time <= :dateTill ';
        }

        return $sql;
    }

    private function getAmountFieldSelectSql(): string
    {
        return '
            CASE
               WHEN (type = :depositType OR type = :transferType) AND recipient_wallet_id = :walletId
                   THEN amount_recipient
               WHEN type = :transferType AND sender_wallet_id = :walletId
                   THEN -amount_sender
               ELSE NULL
            END
        ';
    }

    private function getAmountUsdFieldSelectSql(): string
    {
        return '
            TRUNCATE(
                CASE
                    WHEN (type = :depositType OR type = :transferType) AND recipient_wallet_id = :walletId
                        THEN amount_recipient * IFNULL(er1.rate, 1)
                    WHEN type = :transferType AND sender_wallet_id = :walletId
                        THEN -amount_sender * IFNULL(er2.rate, 1)
                    ELSE NULL
                END,
                2
            )
        ';
    }

    private function getJoinsPeriodSqlPart(): string
    {
        return '
            JOIN wallets recipient_wallet
                ON transactions.recipient_wallet_id = recipient_wallet.id
            JOIN currencies recipient_currencies
                ON recipient_wallet.currency_id = recipient_currencies.id
            LEFT JOIN exchange_rates er1
                ON recipient_currencies.id = er1.currency_from_id
                AND er1.currency_to_id = :usdCurrencyId
                AND er1.date = DATE(date_time)
            LEFT JOIN wallets sender_wallet
                ON transactions.sender_wallet_id = sender_wallet.id
            LEFT JOIN currencies sender_currencies
                ON sender_wallet.currency_id = sender_currencies.id
            LEFT JOIN exchange_rates er2
                ON sender_currencies.id = er2.currency_from_id
                AND er2.currency_to_id = :usdCurrencyId
                AND er2.date = DATE(date_time)
        ';
    }
}

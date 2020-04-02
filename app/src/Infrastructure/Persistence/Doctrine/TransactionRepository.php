<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Wallet\TransactionRepositoryInterface;
use App\Domain\Wallet\Transaction;
use App\Domain\Wallet\Wallet;

class TransactionRepository extends AbstractDoctrineRepository implements TransactionRepositoryInterface
{
    public function repositoryClassName(): string
    {
        return Transaction::class;
    }

    public function getBalance(Wallet $wallet): float
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT SUM(
                CASE
                    WHEN (type = :depositType OR type = :transferType) AND recipient_wallet_id = :walletId
                        THEN amount_recipient
                    WHEN type = :transferType AND sender_wallet_id = :walletId
                        THEN -amount_sender
                    ELSE 0
                END
            ) as balance
            FROM transactions
            WHERE
                sender_wallet_id = :walletId
                OR recipient_wallet_id = :walletId
        ');
        $stmt->execute([
            'depositType' => Transaction::TYPE_DEPOSIT,
            'transferType' => Transaction::TYPE_TRANSFER,
            'walletId' => $wallet->getId(),
        ]);

        return (float) $stmt->fetchColumn();
    }
}

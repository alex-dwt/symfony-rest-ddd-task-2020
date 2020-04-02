<?php

declare(strict_types=1);

namespace App\Application\Bus\MessageHandler;

use App\Application\Bus\Message\CreateDepositTransactionCommand;
use App\Domain\Wallet\Transaction;
use App\Infrastructure\Persistence\Doctrine\TransactionRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDepositTransactionHandler implements MessageHandlerInterface
{
    private TransactionRepository $transactionRepository;

    public function __construct(
        TransactionRepository $transactionRepository
    ) {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(CreateDepositTransactionCommand $command): void
    {
        $transaction = Transaction::createDepositTransaction(
            $command->amount(),
            $command->wallet()
        );

        $this->transactionRepository->add($transaction);
    }
}

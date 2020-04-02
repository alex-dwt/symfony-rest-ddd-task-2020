<?php

declare(strict_types=1);

namespace App\Application\Bus\MessageHandler;

use App\Application\Bus\Message\CreateTransferTransactionCommand;
use App\Application\Service\CurrencyConverter;
use App\Domain\Wallet\Transaction;
use App\Infrastructure\Persistence\Doctrine\TransactionRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateTransferTransactionHandler implements MessageHandlerInterface
{
    private TransactionRepository $transactionRepository;
    private CurrencyConverter $currencyConverter;

    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyConverter $currencyConverter
    ) {
        $this->currencyConverter = $currencyConverter;
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(CreateTransferTransactionCommand $command): void
    {
        $recipientUser = $command->recipientWallet()->getUser();
        $senderUser = $command->senderWallet()->getUser();

        if ($recipientUser === $senderUser) {
            // todo
            throw new \RuntimeException('The same user, this is validation error, 422, should be refactored');
        }

        // todo check balance of the sender

        $amountRecipient = $this->currencyConverter->execute(
            $command->senderWallet()->getCurrency(),
            $command->recipientWallet()->getCurrency(),
            $command->amount()
        );

        $transaction = Transaction::createTransferTransaction(
            $command->amount(),
            $command->senderWallet(),
            $amountRecipient,
            $command->recipientWallet()
        );

        $this->transactionRepository->add($transaction);
    }
}

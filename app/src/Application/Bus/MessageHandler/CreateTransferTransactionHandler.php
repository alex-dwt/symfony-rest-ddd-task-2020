<?php

declare(strict_types=1);

namespace App\Application\Bus\MessageHandler;

use App\Application\Bus\Message\CreateTransferTransactionCommand;
use App\Application\Service\CurrencyConverter;
use App\Domain\Wallet\Transaction;
use App\Domain\Wallet\TransactionRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateTransferTransactionHandler implements MessageHandlerInterface
{
    private TransactionRepositoryInterface $transactionRepository;
    private CurrencyConverter $currencyConverter;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        CurrencyConverter $currencyConverter
    ) {
        $this->currencyConverter = $currencyConverter;
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(CreateTransferTransactionCommand $command): Transaction
    {
        $recipientUser = $command->recipientWallet()->getUser();
        $senderUser = $command->senderWallet()->getUser();

        if ($recipientUser === $senderUser) {
            throw new \RuntimeException('The same user, this is validation error, 422, should be refactored'); // todo
        }
        if ($command->amount() <= 0) {
            throw new \RuntimeException('The zero amount, 422, should be refactored'); // todo
        }

        // todo add lock
        $senderWalletBalance = $this->transactionRepository->getBalance($command->senderWallet());

        if ($senderWalletBalance < $command->amount()) {
            throw new \RuntimeException('Balance is not enough, should be refactored to another error and http code'); // todo
        }

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

        return $transaction;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Bus\MessageHandler;

use App\Application\Bus\Message\CreateDepositTransactionCommand;
use App\Application\Bus\Message\CreateTransferTransactionCommand;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\WalletRepository;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CreateWalletTransactionHandler implements MessageSubscriberInterface
{
    private UserRepositoryInterface $userRepository;
    private WalletRepository $walletRepository;

    public function __construct(UserRepositoryInterface $userRepository, WalletRepository $walletRepository)
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    public function handleCreateDepositTransactionCommand(CreateDepositTransactionCommand $command): void
    {
    }

    public function handleCreateTransferTransactionCommand(CreateTransferTransactionCommand $command): void
    {
    }

    public static function getHandledMessages(): iterable
    {
        yield CreateDepositTransactionCommand::class => [
            'method' => 'handleCreateDepositTransactionCommand',
        ];
        yield CreateTransferTransactionCommand::class => [
            'method' => 'handleCreateTransferTransactionCommand',
        ];
    }
}

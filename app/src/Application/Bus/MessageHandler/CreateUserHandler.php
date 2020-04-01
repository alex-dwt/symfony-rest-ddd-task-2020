<?php

declare(strict_types=1);

namespace App\Application\Bus\MessageHandler;

use App\Application\Bus\Message\CreateUserCommand;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Wallet\Wallet;
use App\Infrastructure\Persistence\Doctrine\WalletRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateUserHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private WalletRepository $walletRepository;

    public function __construct(UserRepositoryInterface $userRepository, WalletRepository $walletRepository)
    {
        $this->userRepository = $userRepository;
        $this->walletRepository = $walletRepository;
    }

    public function __invoke(CreateUserCommand $command): User
    {
        $user = new User(
            $command->name(),
            $command->city(),
            $command->country(),
        );
        $this->userRepository->add($user);

        $wallet = new Wallet($command->currency(), $user);
        $this->walletRepository->add($wallet);

        return $user;
    }
}

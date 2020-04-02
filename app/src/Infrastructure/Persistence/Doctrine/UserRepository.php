<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\User;
use Doctrine\DBAL\LockMode;

class UserRepository extends AbstractDoctrineRepository implements UserRepositoryInterface
{
    public function repositoryClassName(): string
    {
        return User::class;
    }

    public function lockUsers(User ...$users): void
    {
        $this
            ->createQuery('
                SELECT u.id
                FROM \App\Domain\User\User u
                WHERE u.id IN (:ids)
            ')
            ->setParameter(
                'ids',
                array_map(fn (User $user) => $user->getId(), $users)
            )
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getResult();
    }
}

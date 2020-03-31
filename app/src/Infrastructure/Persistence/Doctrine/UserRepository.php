<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\User;

class UserRepository extends AbstractDoctrineRepository implements UserRepositoryInterface
{
    public function repositoryClassName(): string
    {
        return User::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Common\DomainRepository;

/**
 * @method User get(int $id)
 */
interface UserRepositoryInterface extends DomainRepository
{
}

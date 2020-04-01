<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="wallets",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="currency_user_unique_idx", columns={"currency_id", "user_id"})
 *      }
 * )
 */
class Wallet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(nullable=false)
     */
    private Currency $currency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    public function __construct(Currency $currency, User $user)
    {
       $this->currency = $currency;
       $this->user = $user;
    }
}

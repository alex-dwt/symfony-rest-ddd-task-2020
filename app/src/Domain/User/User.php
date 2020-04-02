<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Wallet\Wallet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=3, options={"fixed" = true})
     */
    private string $country;

    /**
     * @var Wallet[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Domain\Wallet\Wallet", mappedBy="user")
     */
    private Collection $wallets;

    public function __construct(
        string $name,
        string $city,
        string $country
    ) {
        $this->name = $name;
        $this->city = $city;
        $this->country = strtoupper($country);

        $this->wallets = new ArrayCollection();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }

    public function getWallet(string $currency): ?Wallet
    {
        foreach ($this->wallets as $wallet) {
            if ($wallet->getCurrency()->getName() === $currency) {
                return $wallet;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

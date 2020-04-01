<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="currencies")
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=3, options={"fixed" = true}, unique=true)
     */
    private string $name;

    /**
     * @var ExchangeRate[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="ExchangeRate", mappedBy="currency")
     */
    private Collection $exchangeRates;

    public function __construct(string $name)
    {
        $this->name = strtolower($name);
        $this->exchangeRates = new ArrayCollection();
    }
}

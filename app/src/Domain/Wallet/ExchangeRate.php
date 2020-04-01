<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="exchange_rates",
 *     indexes={
 *          @ORM\Index(name="date_idx", columns={"date"})
 *      },
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="currency_date_unique_idx", columns={"currency_id", "date"})
 *      }
 * )
 */
class ExchangeRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(type="integer")
     */
    private int $usdRate;

    /**
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="exchangeRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private Currency $currency;

    public function __construct(
        \DateTimeImmutable $date,
        int $usdRate,
        Currency $currency
    ) {
       $this->date = $date;
       $this->usdRate = $usdRate;
       $this->currency = $currency;
    }
}

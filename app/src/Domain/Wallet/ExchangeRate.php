<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="exchange_rates",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="currencies_date_unique_idx",
 *              columns={"currency_from_id", "currency_to_id", "date"}
 *          )
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
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="exchangeRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private Currency $currencyFrom;

    /**
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="exchangeRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private Currency $currencyTo;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private float $rate;

    public function __construct(
        \DateTimeImmutable $date,
        Currency $currencyFrom,
        Currency $currencyTo,
        float $rate
    ) {
       $this->date = $date;
       $this->rate = $rate;
       $this->currencyFrom = $currencyFrom;
       $this->currencyTo = $currencyTo;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}

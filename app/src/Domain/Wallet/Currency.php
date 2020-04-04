<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="currencies")
 */
class Currency
{
    public const USD_NAME = 'usd';

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

    public function __construct(string $name)
    {
        $this->name = strtolower($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

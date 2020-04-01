<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="transactions")
 */
class Transaction
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_TRANSFER = 'transfer';
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $type;

    /**
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Wallet $senderWallet;

    /**
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(nullable=false)
     */
    private Wallet $recipientWallet;

    /**
     * @ORM\Column(type="integer")
     */
    private int $amountSenderCurrency;

    /**
     * @ORM\Column(type="integer")
     */
    private int $amountRecipientCurrency;

    /**
     * @ORM\Column(type="integer")
     */
    private int $amountUsdCurrency;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $dateTime;

    public function __construct(
        string $type,
        ?Wallet $senderWallet,
        Wallet $recipientWallet,
        int $amountSenderCurrency,
        int $amountRecipientCurrency,
        int $amountUsdCurrency
    ) {
       $this->type = $type;
       $this->senderWallet = $senderWallet;
       $this->recipientWallet = $recipientWallet;
       $this->amountSenderCurrency = $amountSenderCurrency;
       $this->amountRecipientCurrency = $amountRecipientCurrency;
       $this->amountUsdCurrency = $amountUsdCurrency;

       $this->dateTime = new \DateTimeImmutable();
    }
}

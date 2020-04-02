<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Wallet\Dto\TransferTransactionAmountDto;
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
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private ?float $amountSender;

    /**
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(nullable=false)
     */
    private Wallet $recipientWallet;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private float $amountRecipient;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $dateTime;

    private function __construct(
        string $type,
        Wallet $recipientWallet,
        float $amountRecipient,
        ?Wallet $senderWallet,
        ?float $amountSender
    ) {
       $this->type = $type;
       $this->senderWallet = $senderWallet;
       $this->recipientWallet = $recipientWallet;
       $this->amountSender = $amountSender;
       $this->amountRecipient = $amountRecipient;

       $this->dateTime = new \DateTimeImmutable();
    }

    public static function createDepositTransaction(
        float $amount,
        Wallet $wallet
    ): self {
        return new self(
            self::TYPE_DEPOSIT,
            $wallet,
            $amount,
            null,
            null
        );
    }

    public static function createTransferTransaction(
        float $amountSender,
        Wallet $senderWallet,
        float $amountRecipient,
        Wallet $recipientWallet
    ): self {
        return new self(
            self::TYPE_TRANSFER,
            $recipientWallet,
            $amountRecipient,
            $senderWallet,
            $amountSender
        );
    }
}

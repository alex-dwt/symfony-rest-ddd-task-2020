<?php declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Bus\Message\CreateDepositTransactionCommand;
use App\Application\Bus\Message\CreateTransferTransactionCommand;
use App\Application\Bus\Message\CreateUserCommand;
use App\Application\Service\CurrenciesRatesFetcher;
use App\Application\Service\CurrencyConverter;
use App\Domain\Wallet\Criteria\CurrencyByNameCriteria;
use App\Domain\User\User;
use App\Domain\Wallet\Currency;
use App\Domain\Wallet\Transaction;
use App\Infrastructure\Persistence\Doctrine\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CreateFakeTransactionsAndRatesCommand extends Command
{
    private const DAYS_COUNT = 35;

    private const CURRENCIES = [
        'gbp',
        'usd',
        'eur',
        'pln',
    ];

    private EntityManagerInterface $em;
    private MessageBusInterface $commandBus;
    private CurrenciesRatesFetcher $currencyRatesFetcher;
    private CurrencyRepository $currencyRepository;
    private CurrencyConverter $currencyConverter;

    private Currency $currencyUsd;

    protected static $defaultName = 'app:create-fake-transactions-and-rates';

    public function __construct(
        EntityManagerInterface $em,
        MessageBusInterface $commandBus,
        CurrenciesRatesFetcher $currencyRatesFetcher,
        CurrencyRepository $currencyRepository,
        CurrencyConverter $currencyConverter
    ) {
        parent::__construct();

        $this->em = $em;
        $this->commandBus = $commandBus;
        $this->currencyRatesFetcher = $currencyRatesFetcher;
        $this->currencyRepository = $currencyRepository;
        $this->currencyConverter = $currencyConverter;

        $this->currencyUsd = $currencyRepository->getOneByCriteria(
            new CurrencyByNameCriteria('usd')
        );
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Create a lot of transactions and exchange rates rows in DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearDB();

        $users = $this->createUsers();
        $this->em->flush();

        $date = new \DateTime();
        $date->modify('-1 month');

        $progressBar = new ProgressBar($output, self::DAYS_COUNT * 2);
        $progressBar->start();

        // exchange rates
        $exchangeRateDate = clone $date;
        for ($i = 0; $i < self::DAYS_COUNT; $i++) {
            $this->currencyRatesFetcher->execute(
                \DateTimeImmutable::createFromMutable($exchangeRateDate)
            );

            $this->em->flush();

            $exchangeRateDate->modify('+1 day');

            $progressBar->advance();
        }

        // transactions
        $transactionsDate = clone $date;
        for ($i = 0; $i < self::DAYS_COUNT; $i++) {
            $this->createTransactions(
                \DateTimeImmutable::createFromMutable($transactionsDate),
                ...$users
            );

            $this->em->flush();

            $transactionsDate->modify('+1 day');

            $progressBar->advance();
        }

        return 0;
    }

    /**
     * @return User[]
     */
    private function createUsers(): array
    {
        /** @var User[] $users */
        $users = [];

        $j = 0;

        foreach (self::CURRENCIES as $currency) {
            for ($i = 0; $i < 2; $i++) {
                /** @var User $user */
                $user = $this->commandBus->dispatch(
                    new CreateUserCommand(
                        'User ' . $j,
                        $this->currencyRepository
                            ->getOneByCriteria(
                                new CurrencyByNameCriteria($currency)
                            ),
                        'BLR',
                        'City ' . $j,
                    ))
                    ->last(HandledStamp::class)
                    ->getResult();

                $users[] = $user;
                $j++;
            }
        }

        return $users;
    }

    public function createTransactions(\DateTimeImmutable $date, User ...$users): void
    {
        // deposits
        foreach ($users as $recipient) {
            $depositAmount = $this->currencyConverter->execute(
                $this->currencyUsd,
                $recipient->getAnyWallet()->getCurrency(),
                rand(10000, 10099) / 100
            );

            /** @var Transaction $transaction */
            $transaction = $this->commandBus->dispatch(
                new CreateDepositTransactionCommand(
                    $recipient->getAnyWallet(),
                    $depositAmount
                ))
                ->last(HandledStamp::class)
                ->getResult();

            $this->setDateTimeForTransaction($transaction, $date);
        }

        // transfers
        foreach ($users as $recipient) {
            foreach ($users as $sender) {
                if ($recipient === $sender) {
                    continue;
                }

                $transactionAmount = $this->currencyConverter->execute(
                    $this->currencyUsd,
                    $sender->getAnyWallet()->getCurrency(),
                    rand(300, 500) / 100
                );

                /** @var Transaction $transaction */
                $transaction = $this->commandBus->dispatch(
                    new CreateTransferTransactionCommand(
                        $sender->getAnyWallet(),
                        $recipient->getAnyWallet(),
                        $transactionAmount
                    ))
                    ->last(HandledStamp::class)
                    ->getResult();

                $this->setDateTimeForTransaction($transaction, $date);
            }
        }
    }

    private function setDateTimeForTransaction(Transaction $transaction, \DateTimeImmutable $dateTime): void
    {
        $reflectionClass = new \ReflectionClass($transaction);
        $reflectionProperty = $reflectionClass->getProperty('dateTime');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue(
            $transaction,
            $dateTime->setTime(rand(1, 20), rand(1, 59), rand(1, 59))
        );
    }

    private function clearDB(): void
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SET FOREIGN_KEY_CHECKS=0;
            truncate table exchange_rates;
            truncate table users;
            truncate table transactions;
            truncate table wallets;
            SET FOREIGN_KEY_CHECKS=1;
        ');

        $stmt->execute();
    }
}

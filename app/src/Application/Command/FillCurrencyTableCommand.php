<?php declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Service\CurrenciesRatesFetcher;
use App\Domain\Wallet\Currency;
use App\Infrastructure\Persistence\Doctrine\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillCurrencyTableCommand extends Command
{
    private EntityManagerInterface $em;
    private CurrenciesRatesFetcher $currencyRatesFetcher;
    private CurrencyRepository $currencyRepository;

    protected static $defaultName = 'app:fill-currency-table';

    public function __construct(
        EntityManagerInterface $em,
        CurrenciesRatesFetcher $currencyRatesFetcher,
        CurrencyRepository $currencyRepository
    ) {
        parent::__construct();

        $this->em = $em;
        $this->currencyRatesFetcher = $currencyRatesFetcher;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Run once a day to get currency rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (Currency::CURRENCIES as $currency) {
            $this->currencyRepository->add(new Currency($currency));
        }

        $this->em->flush();

        return 0;
    }
}

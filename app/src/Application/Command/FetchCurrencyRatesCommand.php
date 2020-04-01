<?php declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Service\CurrenciesRatesFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCurrencyRatesCommand extends Command
{
    private EntityManagerInterface $em;
    private CurrenciesRatesFetcher $currencyRatesFetcher;

    protected static $defaultName = 'app:fetch-currency-rates';

    public function __construct(
        EntityManagerInterface $em,
        CurrenciesRatesFetcher $currencyRatesFetcher
    ) {
        parent::__construct();

        $this->em = $em;
        $this->currencyRatesFetcher = $currencyRatesFetcher;
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
        $this->currencyRatesFetcher->execute();

        $this->em->flush();

        return 0;
    }
}

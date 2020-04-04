<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Service\CsvGenerator;
use App\Domain\Wallet\Criteria\CurrencyByNameCriteria;
use App\Domain\Wallet\Currency;
use App\Domain\Wallet\TransactionRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\CurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reports")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/{id}/period", requirements={"id": "\d+"}, methods={"GET"})
     */
    public function getUserReportForPeriodAction(
        User $user,
        Request $request,
        TransactionRepositoryInterface $transactionRepository,
        CurrencyRepository $currencyRepository,
        CsvGenerator $csvGenerator
    ) {
        if (!$wallet = $user->getWallet(trim((string) $request->get('currency')))) {
            return new JsonResponse(['message' => 'Wallet with this currency is not found'], 404);
        }

        $dateTill = $dateFrom = null;

        if (
            (
                $request->get('dateFrom') !== null
                && !($dateFrom = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->get('dateFrom')))
            )
            || (
                $request->get('dateTill') !== null
                && !($dateTill = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->get('dateTill')))
            )
        ) {
            return new JsonResponse(['message' => 'Date validation error'], 422);
        }

        $transactions = $transactionRepository
            ->getTransactionsForPeriod(
                $wallet,
                $dateFrom,
                $dateTill,
                $currencyRepository->getOneByCriteria(
                    new CurrencyByNameCriteria(Currency::USD_NAME)
                )->getId()
            );

        if ($request->get('csv')) {
            $response = new Response(
                $csvGenerator->execute($transactions)
            );
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="resport.csv"');

            return $response;
        }

        return [
            'items' => $transactions,
        ];
    }

    /**
     * @Route("/{id}/summary", requirements={"id": "\d+"}, methods={"GET"})
     */
    public function getUserSummaryForPeriodAction(
        User $user,
        Request $request,
        TransactionRepositoryInterface $transactionRepository,
        CurrencyRepository $currencyRepository
    ) {
        if (!$wallet = $user->getWallet(trim((string) $request->get('currency')))) {
            return new JsonResponse(['message' => 'Wallet with this currency is not found'], 404);
        }

        $dateTill = $dateFrom = null;

        if (
            (
                $request->get('dateFrom') !== null
                && !($dateFrom = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->get('dateFrom')))
            )
            || (
                $request->get('dateTill') !== null
                && !($dateTill = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $request->get('dateTill')))
            )
        ) {
            return new JsonResponse(['message' => 'Date validation error'], 422);
        }

        return $transactionRepository
            ->getSummaryForPeriod(
                $wallet,
                $dateFrom,
                $dateTill,
                $currencyRepository->getOneByCriteria(
                    new CurrencyByNameCriteria(Currency::USD_NAME)
                )->getId()
            );
    }
}

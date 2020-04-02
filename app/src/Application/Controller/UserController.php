<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Bus\Message\CreateUserCommand;
use App\Domain\Employee\Criteria\CurrencyByNameCriteria;
use App\Domain\Wallet\TransactionRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Doctrine\CurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Annotation\ControllerActionResponseCode;

/**
 * @Route("/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route(methods={"POST"})
     * @ControllerActionResponseCode(201)
     */
    public function createUserAction(
        Request $request,
        CurrencyRepository $currencyRepository
    ) {
        $name = trim((string) $request->get('name'));
        $city = trim((string) $request->get('city'));
        $country = strtoupper(trim((string) $request->get('country')));
        $currency = $currencyRepository
            ->getOneByCriteria(
                new CurrencyByNameCriteria(
                    trim((string) $request->get('currency'))
                )
            );

        if ($name === ''
            || $city === ''
            || !$currency
            || !Countries::alpha3CodeExists($country)
        ) {
            return new JsonResponse(['message' => 'Validation error'], 422);
        }

        /** @var User $user */
        $user = $this->dispatchMessage(
            new CreateUserCommand(
                $name,
                $currency,
                $country,
                $city
            ))
            ->last(HandledStamp::class)
            ->getResult();

        return $user->toArray();
    }

    /**
     * @Route("/{id}/balance", requirements={"id": "\d+"}, methods={"GET"})
     */
    public function getUserBalanceAction(
        User $user,
        Request $request,
        TransactionRepositoryInterface $transactionRepository
    ) {
        if (!$wallet = $user->getWallet(trim((string) $request->get('currency')))) {
            return new JsonResponse(['message' => 'Wallet with this currency is not found'], 404);
        }

        return [
            'balance' => $transactionRepository->getBalance($wallet),
        ];
    }
}

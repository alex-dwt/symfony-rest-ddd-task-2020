<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Bus\Message\CreateDepositTransactionCommand;
use App\Application\Bus\Message\CreateTransferTransactionCommand;
use App\Domain\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transactions")
 */
class TransactionController extends AbstractController
{
    /**
     * @Route("/deposit", methods={"POST"})
     */
    public function createDepositAction(
        Request $request,
        UserRepositoryInterface $userRepository
    ) {
        $amount = trim((string) $request->get('amount'));

        if (!($user = $userRepository->get((int) $request->get('userId')))
            || !($wallet = $user->getWallet((string) $request->get('currency')))
            || !$this->checkAmount($amount)
        ) {
            return new JsonResponse(['message' => 'Validation error'], 422);
        }

        $this->dispatchMessage(
            new CreateDepositTransactionCommand(
                $wallet,
                (float) $amount
            )
        );
    }

    /**
     * @Route("/transfer", methods={"POST"})
     */
    public function createTransferAction(
        Request $request,
        UserRepositoryInterface $userRepository
    ) {
        $amount = trim((string) $request->get('amount'));

        if (!($userFrom = $userRepository->get((int) $request->get('userFromId')))
            || !($walletFrom = $userFrom->getWallet((string) $request->get('currencyFrom')))
            || !($userTo = $userRepository->get((int) $request->get('userToId')))
            || !($walletTo = $userTo->getWallet((string) $request->get('currencyTo')))
            || !$this->checkAmount($amount)
        ) {
            return new JsonResponse(['message' => 'Validation error'], 422);
        }

        $this->dispatchMessage(
            new CreateTransferTransactionCommand(
                $walletFrom,
                $walletTo,
                (float) $amount
            )
        );
    }

    private function checkAmount(string $amount): bool
    {
        if (!preg_match('/^[\d]+(\.[\d]{1,2})?$/', $amount)) {
            return false;
        }

        if ($amount > 1_000_000) {
            return false;
        }

        return true;
    }
}

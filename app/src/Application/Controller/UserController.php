<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Bus\Message\CreateUserCommand;
use App\Domain\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
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
    public function createUserAction(Request $request, MessageBusInterface $commandBus)
    {
        $name = trim((string) $request->get('name'));

        if ($name === '') {
            return new JsonResponse(['message' => 'Validation error'], 422);
        }

        /** @var User $user */
        $user = $commandBus
            ->dispatch(new CreateUserCommand($name))
            ->last(HandledStamp::class)
            ->getResult();

        return $user->toArray();
    }
}

<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\System;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelViewSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $result = (array) $event->getControllerResult();

        $event->setResponse(
            new JsonResponse(
                $result,
                $this->buildStatusCode($event)
            )
        );
    }

    private function buildStatusCode(ViewEvent $event): int
    {
        if ($code = $event
            ->getRequest()
            ->attributes
            ->get(KernelControllerSubscriber::RESPONSE_CODE_PARAM_NAME)
        ) {
            return intval($code);
        }

        $result = $event->getControllerResult();

        if (is_array($result)) {
            return 200;
        }

        return !$result ? 204 : 200;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\System;

use App\Application\Annotation\ControllerActionResponseCode;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelControllerSubscriber implements EventSubscriberInterface
{
    const RESPONSE_CODE_PARAM_NAME = '_app_controller_action_response_code';

    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        if (!is_array($controller)
            || (!$controllerClassInstance = $controller[0] ?? null)
            || (!$controllerActionName = $controller[1] ?? null)
        ) {
            return;
        }

        $object = new \ReflectionClass(\get_class($controllerClassInstance));
        $method = $object->getMethod($controllerActionName);

        if (!$annotation = $this->reader->getMethodAnnotation($method, ControllerActionResponseCode::class)) {
            return;
        }

        /** @var ControllerActionResponseCode $annotation */

        $event->getRequest()->attributes->set(self::RESPONSE_CODE_PARAM_NAME, $annotation->value);
    }
}

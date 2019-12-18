<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Subscriber;

use Pawly\RestApiValidator\Response\ApiResponseInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This subscriber forces to use response via ApiResponse entity
 * rather that using inconsistent way of returning data.
 */
class ValidateResponseInterfaceSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response instanceof ApiResponseInterface) {
            throw new RuntimeException(
                sprintf('Response from your controller must implement %s', ApiResponseInterface::class)
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', 1],
        ];
    }
}

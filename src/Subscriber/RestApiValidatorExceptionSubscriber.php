<?php
declare(strict_types=1);

namespace Pawly\RestApiValidator\Subscriber;

use Pawly\RestApiValidator\Exception\RequestValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This subscriber handle all kind of server exceptions
 * and returns proper JSON response.
 */
class RestApiValidatorExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : $exception->getCode();

        if (0 === $code) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data = [
            'status' => $code,
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof RequestValidationException) {
            $errorArr = [];

            foreach ($exception->getErrors() as $error) {
                $invalidProperty = $error->getPropertyPath();
                $errorArr[$invalidProperty] = $error->getMessage();
            }

            $data['details'] = $errorArr;
        }

        $response = new Response(json_encode($data), $code);
        $response->headers->set('Content-Type', 'application/problem+json');

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 1],
        ];
    }
}

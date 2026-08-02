<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Listener;

use App\Shared\Domain\DomainException;
use App\Shared\Infrastructure\Bus\CommandNotRegisteredError;
use App\Shared\Infrastructure\Bus\QueryNotRegisteredError;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof ValidationFailedException
            || ($throwable->getPrevious() instanceof ValidationFailedException)) {
            $validation = $throwable instanceof ValidationFailedException
                ? $throwable
                : $throwable->getPrevious();

            assert($validation instanceof ValidationFailedException);

            $violations = [];
            foreach ($validation->getViolations() as $violation) {
                $violations[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => (string) $violation->getMessage(),
                ];
            }

            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => 'VALIDATION_FAILED',
                    'message' => 'Request validation failed.',
                    'details' => $violations,
                ],
            ], 422));

            return;
        }

        if ($throwable instanceof DomainException) {
            $status = match ($throwable->errorCode()) {
                'ORDER_NOT_FOUND' => 404,
                'ORDER_ALREADY_CANCELLED' => 409,
                default => 409,
            };

            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => $throwable->errorCode(),
                    'message' => $throwable->getMessage(),
                ],
            ], $status));

            return;
        }

        if ($throwable instanceof CommandNotRegisteredError || $throwable instanceof QueryNotRegisteredError) {
            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => 'An internal error occurred.',
                ],
            ], 500));

            return;
        }

        if ($throwable instanceof HttpExceptionInterface) {
            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => 'HTTP_ERROR',
                    'message' => $throwable->getMessage(),
                ],
            ], $throwable->getStatusCode()));

            return;
        }

        $event->setResponse(new JsonResponse([
            'error' => [
                'code' => 'INTERNAL_ERROR',
                'message' => 'An internal error occurred.',
            ],
        ], 500));
    }
}

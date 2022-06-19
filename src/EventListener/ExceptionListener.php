<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\BookingAlreadyExistsException;
use App\Exception\BookingNotAllowedException;
use App\Exception\ValidationExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private const ERROR_UNEXPECTED_ERROR = 'ERROR_UNEXPECTED_ERROR';
    private const ERROR_VALIDATION = 'ERROR_VALIDATION';
    private const ERROR_BOOKING_EXISTS = 'ERROR_BOOKING_EXISTS';
    private const ERROR_BOOKING_NOT_ALLOWED = 'ERROR_BOOKING_NOT_ALLOWED';

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $error = self::ERROR_UNEXPECTED_ERROR;
        if ($exception instanceof ValidationExceptionInterface) {
            $error = self::ERROR_VALIDATION;
        }

        if ($exception instanceof BookingAlreadyExistsException) {
            $error = self::ERROR_BOOKING_EXISTS;
        }

        if ($exception instanceof BookingNotAllowedException) {
            $error = self::ERROR_BOOKING_NOT_ALLOWED;
        }

        $response = new JsonResponse([
            'error' => $error,
            'success' => false,
        ]);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
        // $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}

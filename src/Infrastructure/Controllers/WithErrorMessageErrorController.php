<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

#[AsController]
readonly class WithErrorMessageErrorController
{
    public function __invoke(\Throwable $exception): Response
    {
        $statusCode = match (get_class($exception)) {
            InvalidArgumentException::class => Response::HTTP_BAD_REQUEST,
            default => null,
        };
        $exception = FlattenException::createFromThrowable($exception);

        /*
         * The difference with the symfony ErrorController is that we get JUST the exception message.
         * The parent uses $exception->getAsString() which apparently renders the HTML output.
         */
        return new JsonResponse(
            ['message' => $exception->getMessage()],
            $statusCode ?? $exception->getStatusCode(),
            $exception->getHeaders(),
        );
    }
}

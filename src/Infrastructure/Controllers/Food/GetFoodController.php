<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers\Food;

use App\Application\Food\GetFoodQuery;
use App\Infrastructure\Bus\QueryBus;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final readonly class GetFoodController
{
    public function __construct(
        private QueryBus $queryBus
    ) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws ExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        int $id,
    ): JsonResponse {
        $response = $this->queryBus->query(
            new GetFoodQuery(
                $id,
            )
        );

        return new JsonResponse($response, Response::HTTP_OK);
    }
}

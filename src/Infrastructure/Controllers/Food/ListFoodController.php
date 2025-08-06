<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers\Food;

use App\Application\Food\ListFoodQuery;
use App\Infrastructure\Bus\QueryBus;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[AsController]
final readonly class ListFoodController
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
        #[MapQueryParameter]
        ?int $pageSize,
        #[MapQueryParameter]
        ?int $pageNumber,
        #[MapQueryParameter]
        ?string $type,
    ): JsonResponse {
        $response = $this->queryBus->query(
            new ListFoodQuery(
                $pageSize,
                $pageNumber,
                $type,
            )
        );

        return new JsonResponse($response, Response::HTTP_OK);
    }
}

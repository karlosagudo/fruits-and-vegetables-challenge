<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\Exceptions\EntityNotFound;
use App\Application\QueryHandlerInterface;
use App\Application\QueryInterface;
use App\Domain\Repositories\FoodRepositoryInterface;

/**
 * @phpstan-import-type FoodFlatten from FoodRepositoryInterface
 */
final readonly class ListFoodQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository
    ) {}

    /**
     * @param ListFoodQuery $query
     *
     * @return array|mixed[]
     *
     * @throws EntityNotFound
     */
    public function handle(QueryInterface $query): array
    {
        $filters = [];
        if ($query->type) {
            $filters = ['type' => $query->type];
        }
        $foods = $this->foodRepository->list(
            filters: $filters,
            order: ['id' => 'DESC'],
            limit: $query->pageSize,
            offset: $query->pageNumber
        );
        if (0 === count($foods)) {
            throw new EntityNotFound('Food not found.');
        }

        return $foods;
    }
}

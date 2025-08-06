<?php

declare(strict_types=1);

namespace App\Application\Food;

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
     */
    public function handle(QueryInterface $query): array
    {
        return $this->foodRepository->list([], null, $query->pageNumber, $query->pageSize);
    }
}

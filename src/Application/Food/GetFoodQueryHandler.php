<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\Exceptions\EntityNotFound;
use App\Application\QueryHandlerInterface;
use App\Application\QueryInterface;
use App\Domain\Models\Food;
use App\Domain\Repositories\FoodRepositoryInterface;

/**
 * @phpstan-import-type FoodFlatten from FoodRepositoryInterface
 */
final readonly class GetFoodQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository
    ) {}

    /**
     * @param GetFoodQuery $query
     *
     * @return Food|FoodFlatten
     *
     * @throws EntityNotFound
     */
    public function handle(QueryInterface $query): array|Food
    {
        $food = $this->foodRepository->find($query->id);
        if (1 !== count($food)) {
            throw new EntityNotFound('Food with id '.$query->id);
        }

        return $food[0];
    }
}

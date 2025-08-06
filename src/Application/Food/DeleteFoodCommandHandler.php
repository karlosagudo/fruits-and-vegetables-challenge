<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Application\Exceptions\EntityNotFound;
use App\Domain\Repositories\FoodRepositoryInterface;

/**
 * @phpstan-import-type FoodFlatten from FoodRepositoryInterface
 */
final readonly class DeleteFoodCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository
    ) {}

    /**
     * @param DeleteFoodCommand $command
     *
     * @throws EntityNotFound
     */
    public function handle(CommandInterface $command): void
    {
        $food = $this->foodRepository->find($command->id, true);
        if (!$food) {
            throw new EntityNotFound('Food with id '.$command->id);
        }

        $this->foodRepository->delete($food);
    }
}

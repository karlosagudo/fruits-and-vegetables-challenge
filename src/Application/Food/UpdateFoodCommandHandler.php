<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Application\Exceptions\EntityNotFound;
use App\Domain\Repositories\FoodRepositoryInterface;

final readonly class UpdateFoodCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository,
    ) {}

    /**
     * @param UpdateFoodCommand $command
     *
     * @throws EntityNotFound
     */
    public function handle(CommandInterface $command): void
    {
        $food = $this->foodRepository->find($command->id, true);
        if (null === $food) {
            throw new EntityNotFound('Food with id '.$command->id);
        }

        $food->update(
            $command->id,
            $command->foodDTO->name,
            $command->foodDTO->type,
            $command->foodDTO->quantity,
            $command->foodDTO->unit,
        );

        $this->foodRepository->save($food);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Domain\Models\Food;
use App\Domain\Repositories\FoodRepositoryInterface;

final readonly class CreateFoodCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository,
    ) {}

    /**
     * @param CreateFoodCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $food = Food::create(
            $command->foodDTO->id,
            $command->foodDTO->name,
            $command->foodDTO->type,
            $command->foodDTO->quantity,
            $command->foodDTO->unit,
        );

        $this->foodRepository->save($food);
    }
}

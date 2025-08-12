<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Application\Exceptions\InvalidUnit;
use App\Domain\Exceptions\InvalidUnitDomain;
use App\Domain\Models\Food;
use App\Domain\Repositories\FoodRepositoryInterface;

final readonly class CreateFoodCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository,
    ) {}

    /**
     * @param CreateFoodCommand $command
     *
     * @throws InvalidUnit
     */
    public function handle(CommandInterface $command): void
    {
        try {
            $food = Food::create(
                $command->foodDTO->id,
                $command->foodDTO->name,
                $command->foodDTO->type,
                $command->foodDTO->quantity,
                $command->foodDTO->unit,
            );
        } catch (InvalidUnitDomain $exception) {
            throw new InvalidUnit($exception->getMessage());
        }

        $this->foodRepository->save($food);
    }
}

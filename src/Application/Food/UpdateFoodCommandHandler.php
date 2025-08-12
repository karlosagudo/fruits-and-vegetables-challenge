<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Application\Exceptions\EntityNotFound;
use App\Application\Exceptions\InvalidUnit;
use App\Domain\Exceptions\InvalidUnitDomain;
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
     * @throws InvalidUnit
     */
    public function handle(CommandInterface $command): void
    {
        $food = $this->foodRepository->find($command->id, true);
        if (null === $food) {
            throw new EntityNotFound('Food with id '.$command->id);
        }

        try {
            $food->update(
                $command->id,
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

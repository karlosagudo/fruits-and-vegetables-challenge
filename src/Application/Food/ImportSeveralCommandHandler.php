<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandHandlerInterface;
use App\Application\CommandInterface;
use App\Domain\Models\Food;
use App\Domain\Repositories\FoodRepositoryInterface;
use App\Infrastructure\DTO\FoodDTO;

final readonly class ImportSeveralCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private FoodRepositoryInterface $foodRepository,
    ) {}

    /**
     * @param ImportSeveralCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $getFoodsIds = array_map(function (FoodDTO $foodDto) {
            return $foodDto->id;
        }, $command->foodDTOCollection);

        $toUpdate = $this->foodRepository->getByIds($getFoodsIds);
        $toUpdateIdValues = [];
        foreach ($toUpdate as $value) {
            $toUpdateIdValues[$value->id] = $value;
        }

        foreach ($command->foodDTOCollection as $foodDto) {
            /** @var FoodDTO $foodDto */
            $food = $this->upsert($foodDto, $toUpdateIdValues);
            $this->foodRepository->persist($food);
        }
        $this->foodRepository->flush();
    }

    /**
     * @param array<int, Food> $toUpdateIdValue
     */
    private function upsert(FoodDTO $foodDto, array $toUpdateIdValue): Food
    {
        if (in_array($foodDto->id, array_keys($toUpdateIdValue), true)) {
            /** @var Food $food */
            $food = $toUpdateIdValue[$foodDto->id];
            $food->update(
                id: $foodDto->id,
                name: $foodDto->name,
                type: $foodDto->type,
                quantity: $foodDto->quantity,
                unit: $foodDto->unit
            );

            return $food;
        }

        return Food::create(
            id: $foodDto->id,
            name: $foodDto->name,
            type: $foodDto->type,
            quantity: $foodDto->quantity,
            unit: $foodDto->unit
        );
    }
}

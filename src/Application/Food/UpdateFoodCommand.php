<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandInterface;
use App\Infrastructure\DTO\FoodDTO;

final readonly class UpdateFoodCommand implements CommandInterface
{
    public function __construct(
        public int $id,
        public FoodDTO $foodDTO,
    ) {}
}

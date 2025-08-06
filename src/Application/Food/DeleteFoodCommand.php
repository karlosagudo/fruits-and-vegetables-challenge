<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandInterface;

final readonly class DeleteFoodCommand implements CommandInterface
{
    public function __construct(
        public int $id,
    ) {}
}

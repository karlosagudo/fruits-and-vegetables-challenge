<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\QueryInterface;

final readonly class GetFoodQuery implements QueryInterface
{
    public function __construct(
        public int $id,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\QueryInterface;

final readonly class ListFoodQuery implements QueryInterface
{
    public function __construct(
        public ?int $pageSize = null,
        public ?int $pageNumber = null,
        public ?string $type = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Application\Food;

use App\Application\CommandInterface;
use App\Infrastructure\DTO\FoodDTO;

final readonly class ImportSeveralCommand implements CommandInterface
{
    /**
     * @param FoodDTO[] $foodDTOCollection
     */
    public function __construct(
        public array $foodDTOCollection,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use App\Domain\Models\FoodType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 */
class FoodDTO
{
    public function __construct(
        public int $id,
        #[Assert\Length(max:255)]
        public string $name,
        public FoodType $type,
        public int $quantity,
        #[Assert\Length(max:255)]
        public string $unit,
    ) {}
}

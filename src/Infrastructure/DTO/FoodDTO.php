<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FoodDTO
{
    public function __construct(
        public int $id,
        #[Assert\Length(max:255)]
        public string $name,
        #[Assert\Length(max:255)]
        public string $type,
        public int $quantity,
        #[Assert\Length(max:255)]
        public string $unit,
    ) {}
}

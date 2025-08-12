<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Exceptions\InvalidUnitDomain;

class Food
{
    /**
     * @param string   $name     Ex: sample text
     * @param FoodType $type     Ex: sample text
     * @param int      $quantity Ex: 6
     * @param string   $unit     Ex: sample text
     */
    public function __construct(
        public int $id,
        public string $name,
        public FoodType $type,
        public int $quantity,
        public string $unit,
    ) {}

    /**
     * @param string   $name     Ex: sample text
     * @param FoodType $type     Ex: sample text
     * @param int      $quantity Ex: 6
     * @param string   $unit     Ex: sample text
     *
     * @throws InvalidUnitDomain
     */
    public static function create(
        int $id,
        string $name,
        FoodType $type,
        int $quantity,
        string $unit,
    ): self {
        $quantity = self::convertQuantityToGrams($quantity, $unit);

        return new Food(
            $id,
            $name,
            $type,
            $quantity,
            'g',
        );
    }

    /**
     * @param string   $name     Ex: sample text
     * @param FoodType $type     Ex: sample text
     * @param int      $quantity Ex: 6
     * @param string   $unit     Ex: sample text
     *
     * @throws InvalidUnitDomain
     */
    public function update(
        int $id,
        string $name,
        FoodType $type,
        int $quantity,
        string $unit,
    ): self {
        $quantity = self::convertQuantityToGrams($quantity, $unit);
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->unit = 'g';

        return $this;
    }

    /**
     * @throws InvalidUnitDomain
     */
    private static function convertQuantityToGrams(int $quantity, string $unit): int
    {
        return match ($unit) {
            'g' => $quantity,
            'kg' => $quantity * 1000,
            default => throw new InvalidUnitDomain('The Unit '.$unit.' does not exists'),
        };
    }
}

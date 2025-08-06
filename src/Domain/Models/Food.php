<?php

declare(strict_types=1);

namespace App\Domain\Models;

class Food
{
    /**
     * @param string $name Ex: sample text
     * @param string $type Ex: sample text
     * @param int $quantity Ex: 6
     * @param string $unit Ex: sample text                    */
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
        public int $quantity,
        public string $unit,
    ) {}

    /**
     * @param string $name Ex: sample text
     * @param string $type Ex: sample text
     * @param int $quantity Ex: 6
     * @param string $unit Ex: sample text                    */
    public static function create(
        int $id,
        string $name,
        string $type,
        int $quantity,
        string $unit,
    ): self {
        return new Food(
            $id,
            $name,
            $type,
            $quantity,
            $unit,
        );
    }

    /**
     * @param string $name Ex: sample text
     * @param string $type Ex: sample text
     * @param int $quantity Ex: 6
     * @param string $unit Ex: sample text                    */
    public function update(
        int $id,
        string $name,
        string $type,
        int $quantity,
        string $unit,
    ): self {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->unit = $unit;

        return $this;
    }
}

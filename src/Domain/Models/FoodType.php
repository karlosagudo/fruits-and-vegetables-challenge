<?php

declare(strict_types=1);

namespace App\Domain\Models;

enum FoodType: string
{
    case VEGETABLE = 'vegetable';
    case FRUIT = 'fruit';
}

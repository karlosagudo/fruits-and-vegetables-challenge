<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Food\CreateFoodCommand;
use App\Domain\Models\FoodType;
use App\Infrastructure\DTO\FoodDTO;
use App\Tests\HelpersTest\createDTOMockAndRepoForClass;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @group Unit
 *
 * @covers \App\Application\Food\CreateFoodCommand
 */
class CreateFoodCommandTest extends TestCase
{
    use createDTOMockAndRepoForClass;

    public function testCreate(): void
    {
        $faker = Factory::create('fr_FR');
        $newId = 1;
        $name = $faker->text(255);
        $type = $faker->randomElement(FoodType::cases());
        $quantity = $faker->numberBetween(1, 1000);
        $unit = $faker->text(255);

        $entityDto = new FoodDTO(
            $newId,
            $name,
            $type,
            $quantity,
            $unit,
        );
        $expected = new CreateFoodCommand($entityDto);

        self::assertEquals($expected, new CreateFoodCommand($entityDto));
    }
}

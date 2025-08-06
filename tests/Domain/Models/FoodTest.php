<?php

declare(strict_types=1);

namespace App\Tests\Domain\Models;

use App\Domain\Models\Food;
use App\Tests\HelpersTest\TestDomainEvents;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Domain\Models\Food
 *
 * @internal
 */
final class FoodTest extends TestCase
{
    use TestDomainEvents;

    protected function setUp(): void {}

    public function testCreate(): Food
    {
        $faker = Factory::create('fr_FR');

        $food = Food::create(
            1,
            $faker->text(255),
            $faker->text(255),
            $faker->numberBetween(1, 1000),
            $faker->text(255),
        );

        $this->assertInstanceOf(Food::class, $food);

        return $food;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Food $food): void
    {
        $faker = Factory::create('fr_FR');
        $original = clone $food;
        $food->update(
            $faker->numberBetween(1, 10),
            $faker->text(255),
            $faker->text(255),
            $faker->numberBetween(1, 1000),
            $faker->text(255),
        );
        $this->assertNotSame($original, $food);
    }
}

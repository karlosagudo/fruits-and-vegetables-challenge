<?php

declare(strict_types=1);

namespace App\Infrastructure\DB\Fixtures;

use App\Domain\Models\Food;
use App\Domain\Models\FoodType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FoodTestFixture extends Fixture implements FixtureGroupInterface
{
    public const NUMBER_OBJECT = 10;

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $units = ['g', 'kg'];
        for ($i = 0; $i < self::NUMBER_OBJECT; ++$i) {
            $id = $i + 1;

            $food = Food::create(
                id: $id,
                name: $faker->text(255),
                type: $faker->randomElement(FoodType::cases()),
                quantity: $faker->numberBetween(1, 1000),
                unit: $faker->randomElement($units),
            );

            $manager->persist($food);
            $this->addReference('Food'.$i, $food);
            $this->addReference('FoodWithId'.$id, $food);
        }
        $manager->flush();
    }

    /**
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['test'];
    }
}

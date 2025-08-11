<?php

declare(strict_types=1);

namespace App\Infrastructure\DB\Fixtures;

use App\Domain\Models\Food;
use App\Domain\Models\FoodType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FoodDevFixture extends Fixture implements FixtureGroupInterface
{
    public const NUMBER_OBJECT = 10;

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < self::NUMBER_OBJECT; ++$i) {
            $id = $i + 1;

            $food = new Food(
                $id,
                $faker->text(255),
                $faker->randomElement(FoodType::cases()),
                $faker->numberBetween(1, 1000),
                $faker->text(255),
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
        return ['develop'];
    }
}

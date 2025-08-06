<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Food\CreateFoodCommand;
use App\Application\Food\CreateFoodCommandHandler;
use App\Domain\Models\Food;
use App\Domain\Repositories\FoodRepositoryInterface;
use App\Infrastructure\DTO\FoodDTO;
use App\Tests\HelpersTest\createDTOMockAndRepoForClass;
use App\Tests\HelpersTest\TestDomainEvents;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Application\Exceptions\EntityNotFound
 * @covers \App\Application\Food\CreateFoodCommand
 * @covers \App\Application\Food\CreateFoodCommandHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Repositories\DoctrineFoodRepository
 *
 * @internal
 */
final class CreateFoodCommandHandlerTest extends TestCase
{
    use TestDomainEvents;
    use createDTOMockAndRepoForClass;

    public function testFoodWithId(): void
    {
        $faker = Factory::create('fr_FR');
        $repository = $this->createMock(FoodRepositoryInterface::class);

        $newId = 1;
        $name = $faker->text(255);
        $type = $faker->text(255);
        $quantity = $faker->numberBetween(1, 1000);
        $unit = $faker->text(255);

        $fakeCommand = new CreateFoodCommand(
            new FoodDTO(
                $newId,
                $name,
                $type,
                $quantity,
                $unit,
            )
        );

        $class = Food::create(
            $newId,
            $name,
            $type,
            $quantity,
            $unit,
        );
        $repository->expects(self::once())
            ->method('save')
            ->with($class)
        ;

        $sutCommandHandler = new CreateFoodCommandHandler(
            $repository
        );
        $sutCommandHandler->handle($fakeCommand);
    }
}

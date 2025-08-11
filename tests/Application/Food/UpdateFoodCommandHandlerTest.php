<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Exceptions\EntityNotFound;
use App\Application\Food\UpdateFoodCommand;
use App\Application\Food\UpdateFoodCommandHandler;
use App\Domain\Models\FoodType;
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
 * @covers \App\Application\Food\UpdateFoodCommand
 * @covers \App\Application\Food\UpdateFoodCommandHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Repositories\DoctrineFoodRepository
 *
 * @internal
 */
final class UpdateFoodCommandHandlerTest extends TestCase
{
    use TestDomainEvents;
    use createDTOMockAndRepoForClass;

    public function testFoodUpdateNotFound(): void
    {
        $faker = Factory::create('fr_FR');
        $repository = $this->createMock(FoodRepositoryInterface::class);

        $id = 1;
        $fakeCommand = new UpdateFoodCommand(
            $id,
            new FoodDTO(
                1,
                $faker->name(),
                $faker->randomElement(FoodType::cases()),
                $faker->numberBetween(1, 1000),
                $faker->text(),
            )
        );

        $repository->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn(null)
        ;

        $sutCommandHandler = new UpdateFoodCommandHandler(
            $repository
        );
        $this->expectException(EntityNotFound::class);
        $this->expectExceptionMessage('Food with id '.$id);
        $sutCommandHandler->handle($fakeCommand);
    }

    public function testFoodUpdateFound(): void
    {
        $faker = Factory::create('fr_FR');
        $repository = $this->createMock(FoodRepositoryInterface::class);
        $id = 1;

        $newId = 1;
        $name = $faker->text(255);
        $type = $faker->randomElement(FoodType::cases());
        $quantity = $faker->numberBetween(1, 1000);
        $unit = $faker->text(255);

        $dto = new FoodDTO($newId, $name, $type, $quantity, $unit);
        $fakeCommand = new UpdateFoodCommand($id, $dto);
        $fakeObject = $this->createMock('App\Domain\Models\Food');
        $fakeObject->expects(self::once())
            ->method('update')
            ->with($id, $name, $type, $quantity, $unit)
        ;

        $repository->expects(self::once())
            ->method('find')
            ->with($id, true)
            ->willReturn($fakeObject)
        ;

        $repository->expects(self::once())
            ->method('save')
            ->with($fakeObject)
        ;

        $sutCommandHandler = new UpdateFoodCommandHandler(
            $repository
        );
        $sutCommandHandler->handle($fakeCommand);
    }
}

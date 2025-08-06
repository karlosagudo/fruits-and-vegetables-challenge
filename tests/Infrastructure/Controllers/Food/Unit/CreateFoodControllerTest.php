<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food\Unit;

use App\Application\Food\CreateFoodCommand;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Controllers\Food\CreateFoodController;
use App\Infrastructure\DTO\FoodDTO;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Unit
 *
 * @internal
 *
 * @covers \App\Infrastructure\Controllers\Food\CreateFoodController
 */
class CreateFoodControllerTest extends TestCase
{
    public function testHappyPath(): void
    {
        // Given
        $faker = Factory::create('fr_FR');
        $expected = new JsonResponse(null, Response::HTTP_CREATED);

        $dto = new FoodDTO(
            1,
            $faker->name(),
            $faker->text(),
            $faker->numberBetween(1, 1000),
            $faker->text(),
        );
        $commandBusMock = $this->createMock(CommandBusInterface::class);
        $commandBusMock->expects(self::once())
            ->method('handle')
            ->with(
                new CreateFoodCommand(
                    $dto,
                )
            )
        ;
        $sut = new CreateFoodController($commandBusMock);

        // When
        $result = ($sut)(
            $dto,
        );

        // Then
        self::assertEquals($expected, $result);
    }
}

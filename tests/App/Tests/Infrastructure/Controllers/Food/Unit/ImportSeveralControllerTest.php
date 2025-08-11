<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food\Unit;

use App\Application\Food\ImportSeveralCommand;
use App\Domain\Models\FoodType;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Controllers\Food\Import\ImportSeveralController;
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
 * @covers \App\Infrastructure\Controllers\Food\Import\ImportSeveralController
 */
class ImportSeveralControllerTest extends TestCase
{
    public function testHappyPath(): void
    {
        // Given
        $faker = Factory::create('fr_FR');
        $expected = new JsonResponse(null, Response::HTTP_CREATED);

        $dto = new FoodDTO(
            1,
            $faker->name(),
            $faker->randomElement(FoodType::cases()),
            $faker->numberBetween(1, 1000),
            $faker->text(),
        );
        $commandBusMock = $this->createMock(CommandBusInterface::class);
        $commandBusMock->expects(self::once())
            ->method('handle')
            ->with(
                new ImportSeveralCommand(
                    [$dto],
                )
            )
        ;
        $sut = new ImportSeveralController($commandBusMock);

        // When
        $result = ($sut)(
            [$dto],
        );

        // Then
        self::assertEquals($expected, $result);
    }
}

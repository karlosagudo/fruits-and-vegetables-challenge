<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food\Unit;

use App\Application\Food\DeleteFoodCommand;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Controllers\Food\DeleteFoodController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Unit
 *
 * @internal
 *
 * @covers \App\Infrastructure\Controllers\Food\DeleteFoodController
 */
class DeleteFoodControllerTest extends TestCase
{
    public function testHappyPath(): void
    {
        // Given
        $expected = new JsonResponse(null, Response::HTTP_ACCEPTED);
        $id = 1;

        $commandBusMock = $this->createMock(CommandBusInterface::class);
        $commandBusMock->expects(self::once())
            ->method('handle')
            ->with(
                new DeleteFoodCommand(
                    $id,
                )
            )
        ;
        $sut = new DeleteFoodController($commandBusMock);

        // When
        $result = ($sut)(
            $id,
        );

        // Then
        self::assertEquals($expected, $result);
    }
}

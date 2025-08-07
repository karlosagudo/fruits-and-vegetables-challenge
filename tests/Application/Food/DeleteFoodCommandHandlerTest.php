<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Exceptions\EntityNotFound;
use App\Application\Food\DeleteFoodCommand;
use App\Application\Food\DeleteFoodCommandHandler;
use App\Domain\Repositories\FoodRepositoryInterface;
use App\Tests\HelpersTest\createDTOMockAndRepoForClass;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Application\Food\DeleteFoodCommand
 * @covers \App\Application\Food\DeleteFoodCommandHandler
 * @covers \App\Infrastructure\Repositories\DoctrineFoodRepository
 *
 * @internal
 */
class DeleteFoodCommandHandlerTest extends TestCase
{
    use createDTOMockAndRepoForClass;

    public function testFoodFindById(): void
    {
        [$foodMock, , $mockRepository] = $this->createMockDTOAndRepo(
            'Food',
            false,
            'int',
        );
        $mockRepository->expects(self::once())->method('find')->with(1, true)->willReturn($foodMock);
        $mockRepository->expects(self::once())->method('delete')->with($foodMock);
        $command = new DeleteFoodCommand(1);
        $sutHandler = new DeleteFoodCommandHandler($mockRepository);
        $sutHandler->handle($command);
    }

    public function testFoodNotFound(): void
    {
        $id = random_int(1, 100);
        $mockRepository = $this->createMock(FoodRepositoryInterface::class);
        $mockRepository->expects(self::once())->method('find')
            ->with($id, true)->willReturn(null)
        ;

        $query = new DeleteFoodCommand($id);
        $sutHandler = new DeleteFoodCommandHandler($mockRepository);
        $this->expectException(EntityNotFound::class);
        $this->expectExceptionMessage('Food with id '.$id);
        $sutHandler->handle($query);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Exceptions\EntityNotFound;
use App\Application\Food\GetFoodQuery;
use App\Application\Food\GetFoodQueryHandler;
use App\Domain\Repositories\FoodRepositoryInterface;
use App\Tests\HelpersTest\createDTOMockAndRepoForClass;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Application\Food\GetFoodQuery
 * @covers \App\Application\Food\GetFoodQueryHandler
 * @covers \App\Infrastructure\Repositories\DoctrineFoodRepository
 *
 * @internal
 */
class GetFoodQueryHandlerTest extends TestCase
{
    use createDTOMockAndRepoForClass;

    public function testFoodFindById(): void
    {
        [$foodMock, , $mockRepository] = $this->createMockDTOAndRepo(
            'Food',
            false,
            'int',
        );
        $mockRepository->expects(self::once())->method('find')->willReturn([['found']]);
        $query = new GetFoodQuery(1);
        $sutHandler = new GetFoodQueryHandler($mockRepository);
        $sutHandler->handle($query);
    }

    public function testFoodNotFound(): void
    {
        $mockRepository = $this->createMock(FoodRepositoryInterface::class, false);
        $mockRepository->expects(self::once())->method('find')->willReturn([]);
        $id = 1;
        $query = new GetFoodQuery($id);
        $sutHandler = new GetFoodQueryHandler($mockRepository);
        $this->expectException(EntityNotFound::class);
        $this->expectExceptionMessage('Food with id '.$id);
        $sutHandler->handle($query);
    }
}

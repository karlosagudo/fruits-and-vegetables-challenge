<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Exceptions\EntityNotFound;
use App\Application\Food\ListFoodQuery;
use App\Application\Food\ListFoodQueryHandler;
use App\Domain\Models\FoodType;
use App\Domain\Repositories\FoodRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 *
 * @covers \App\Application\Exceptions\EntityNotFound
 * @covers \App\Application\Food\ListFoodQuery
 * @covers \App\Application\Food\ListFoodQueryHandler
 * @covers \App\Infrastructure\Repositories\DoctrineFoodRepository
 *
 * @internal
 */
class ListFoodQueryHandlerTest extends TestCase
{
    public function testFoodList(): void
    {
        $expected = ['something', 'another'];
        $mockRepository = $this->createMock(FoodRepositoryInterface::class);
        $mockRepository
            ->expects(self::once())
            ->method('list')
            ->with([], ['id' => 'DESC'], null, null)
            ->willReturn($expected)
        ;
        $query = new ListFoodQuery();
        $sutHandler = new ListFoodQueryHandler($mockRepository);
        $result = $sutHandler->handle($query);
        $this->assertSame($expected, $result);
    }

    public function testFoodListNotFound(): void
    {
        $mockRepository = $this->createMock(FoodRepositoryInterface::class);
        $mockRepository
            ->expects(self::once())
            ->method('list')
            ->with([], ['id' => 'DESC'], null, null)
            ->willReturn([])
        ;
        $query = new ListFoodQuery();
        $sutHandler = new ListFoodQueryHandler($mockRepository);
        $this->expectException(EntityNotFound::class);
        $this->expectExceptionMessage('Food not found');
        $sutHandler->handle($query);
    }

    public function testFoodListWithPaginate(): void
    {
        $pageSize = random_int(1, 100);
        $pageNumber = random_int(0, 25);
        $query = new ListFoodQuery(pageSize: $pageSize, pageNumber: $pageNumber);
        $mockRepository = $this->createMock(FoodRepositoryInterface::class);
        $mockRepository
            ->expects(self::once())
            ->method('list')
            ->with([], ['id' => 'DESC'], $pageSize, $pageNumber)
            ->willReturn(['something'])
        ;

        $sutHandler = new ListFoodQueryHandler($mockRepository);
        $sutHandler->handle($query);
    }

    public function testFoodListWithPaginateAndType(): void
    {
        $pageSize = random_int(1, 100);
        $pageNumber = random_int(0, 25);
        $type = FoodType::cases()[random_int(0, count(FoodType::cases()) - 1)];
        $query = new ListFoodQuery(pageSize: $pageSize, pageNumber: $pageNumber, type: $type->value);
        $mockRepository = $this->createMock(FoodRepositoryInterface::class);
        $mockRepository
            ->expects(self::once())
            ->method('list')
            ->with(['type' => $type->value], ['id' => 'DESC'], $pageSize, $pageNumber)
            ->willReturn(['something'])
        ;

        $sutHandler = new ListFoodQueryHandler($mockRepository);
        $sutHandler->handle($query);
    }
}

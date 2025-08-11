<?php

declare(strict_types=1);

namespace App\Tests\Application\Food;

use App\Application\Food\ImportSeveralCommand;
use App\Application\Food\ImportSeveralCommandHandler;
use App\Domain\Models\Food;
use App\Domain\Models\FoodType;
use App\Domain\Repositories\FoodRepositoryInterface;
use App\Infrastructure\DTO\FoodDTO;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ImportSeveralCommandHandlerTest extends TestCase
{
    public function testHappyPathAllCases(): void
    {
        // given
        $command = new ImportSeveralCommand([
            new FoodDTO(
                id: 1,
                name: 'Carrot',
                type: FoodType::VEGETABLE,
                quantity: 6,
                unit: 'g'
            ),
            new FoodDTO(
                id: 2,
                name: 'Beans',
                type: FoodType::VEGETABLE,
                quantity: 60,
                unit: 'g'
            ),
        ]);
        //        $carrotExists = Food::create(
        //            id: 1, name: 'Carrot', type: FoodType::VEGETABLE, quantity: 6, unit: 'g'
        //        );
        $carrotExists = $this->createMock(Food::class);
        $carrotExists->expects(self::once())
            ->method('update')
            ->with(id: 1, name: 'Carrot', type: FoodType::VEGETABLE, quantity: 6, unit: 'g')
        ;
        $carrotExists->id = 1;

        $beansCreated = Food::create(
            id: 2,
            name: 'Beans',
            type: FoodType::VEGETABLE,
            quantity: 60,
            unit: 'g'
        );
        $repository = $this->createMock(FoodRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('getByIds')
            ->with([1, 2])
            ->willReturn([$carrotExists])
        ;
        $matcher = self::exactly(2);
        $repository->expects($matcher)
            ->method('persist')
            ->willReturnCallback(function (Food $value) use ($matcher, $carrotExists, $beansCreated) {
                match ($matcher->getInvocationCount()) {
                    1 => $this->assertEquals($carrotExists, $value),
                    2 => $this->assertEquals($beansCreated, $value),
                };
            })
        ;
        $repository->expects(self::once())
            ->method('flush')
        ;
        // then
        $sut = new ImportSeveralCommandHandler($repository);
        $sut->handle($command);
    }
}

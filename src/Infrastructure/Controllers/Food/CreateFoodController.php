<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers\Food;

use App\Application\Food\CreateFoodCommand;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\DTO\FoodDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[AsController]
final readonly class CreateFoodController
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}

    public function __invoke(
        #[MapRequestPayload]
        FoodDTO $foodDTO,
    ): JsonResponse {
        $this->commandBus->handle(new CreateFoodCommand($foodDTO));

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}

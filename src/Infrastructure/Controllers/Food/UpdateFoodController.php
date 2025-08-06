<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers\Food;

use App\Application\Food\UpdateFoodCommand;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\DTO\FoodDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[AsController]
final readonly class UpdateFoodController
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}

    public function __invoke(
        int $id,
        #[MapRequestPayload]
        FoodDTO $foodDTO,
    ): JsonResponse {
        $this->commandBus->handle(new UpdateFoodCommand($id, $foodDTO));

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}

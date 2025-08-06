<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers\Food;

use App\Application\Food\DeleteFoodCommand;
use App\Infrastructure\Bus\CommandBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final readonly class DeleteFoodController
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}

    public function __invoke(
        int $id,
    ): JsonResponse {
        $this->commandBus->handle(new DeleteFoodCommand($id));

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers\Food\Import;

use App\Application\Food\ImportSeveralCommand;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\DTO\FoodDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[AsController]
final readonly class ImportSeveralController
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}

    /**
     * @param FoodDTO[] $importFoodDTO
     */
    public function __invoke(
        #[MapRequestPayload(type: FoodDTO::class)]
        array $importFoodDTO,
    ): JsonResponse {
        $this->commandBus->handle(new ImportSeveralCommand($importFoodDTO));

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}

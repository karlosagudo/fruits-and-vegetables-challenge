<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\CommandMiddleWares;

use App\Application\CommandInterface;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseTransactionMiddleware implements CommandMiddlewareInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(CommandInterface $command, callable $nextMiddleware): void
    {
        $connection = $this->entityManager->getConnection();

        try {
            $connection->beginTransaction();
            $nextMiddleware($command);
            $connection->commit();
        } catch (\Throwable $throwable) {
            $connection->rollBack();

            throw $throwable;
        }
    }
}

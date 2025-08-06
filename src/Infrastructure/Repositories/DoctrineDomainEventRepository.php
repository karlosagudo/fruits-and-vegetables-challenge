<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Models\DomainEventModel;
use App\Domain\Repositories\DomainEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final readonly class DoctrineDomainEventRepository implements DomainEventRepositoryInterface
{
    /**
     * @var EntityRepository<DomainEventModel>
     */
    private EntityRepository $entityRepository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->entityRepository = $entityManager->getRepository(DomainEventModel::class);
    }

    public function save(DomainEventModel $domainEventModel): void
    {
        $this->entityManager->persist($domainEventModel);
        $this->entityManager->flush();
    }

    public function find(string $id): ?DomainEventModel
    {
        return $this->entityRepository->find($id);
    }
}

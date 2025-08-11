<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Models\Food;
use App\Domain\Repositories\FoodRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

/**
 * @phpstan-import-type FoodFlatten from FoodRepositoryInterface
 */
final readonly class DoctrineFoodRepository implements FoodRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * @var EntityRepository<Food>
     */
    private ObjectRepository $entityRepository;

    public function __construct(
        private ManagerRegistry $managerRegistry
    ) {
        $em = $this->managerRegistry->getManagerForClass(Food::class);
        if (!$em instanceof EntityManagerInterface) {
            throw new \Exception('Can not load entity manager');
        }
        $this->entityManager = $em;
        $this->entityRepository = $this->entityManager->getRepository(Food::class);
    }

    public function save(Food $food): void
    {
        $this->entityManager->persist($food);
        $this->entityManager->flush();
    }

    public function persist(Food $food): void
    {
        $this->entityManager->persist($food);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @return ($returnEntity is true ? null|Food: FoodFlatten[])
     */
    public function find(int $id, bool $returnEntity = false): null|array|Food
    {
        $queryBuilder = $this->fullRelationsQuery()
            ->where('e.id = :entityId')
            ->setParameter(
                'entityId',
                $id
            )
        ;
        if ($returnEntity) {
            return $queryBuilder->getQuery()->getResult()[0] ?? null;
        }

        return array_map(self::flattenEntity(...), $queryBuilder->getQuery()->getArrayResult());
    }

    public function delete(Food $food): void
    {
        $this->entityManager->remove($food);
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed>      $filters
     * @param null|array<string,string> $order
     *
     * @return FoodFlatten[] array
     */
    public function list(array $filters, ?array $order, ?int $limit = null, ?int $offset = null): array
    {
        // / we get the distincts to get all objects real paginated not paginate rows
        $queryBuilderDistinct = $this->fullRelationsQuery()
            ->select('DISTINCT e.id')
        ;

        $queryBuilderDistinct = $this->executeFilterRelations($filters, $queryBuilderDistinct);
        $queryBuilderDistinct = $this->executePagination($limit, $offset, $queryBuilderDistinct);
        $queryDistinctIds = $queryBuilderDistinct->getQuery()->getArrayResult();

        $distinctIds = array_map(
            static fn ($value) => $value['id'],
            $queryDistinctIds
        );

        $finalQuery = $this->fullRelationsQuery()
            ->andWhere('e.id IN (:ids)')
            ->setParameter('ids', $distinctIds)
        ;

        if (null !== $order && [] !== $order) {
            foreach ($order as $key => $direction) {
                $finalQuery = $finalQuery->orderBy('e.'.$key, $direction);
            }
        }

        $objects = $finalQuery->getQuery()->getArrayResult();

        return array_map(self::flattenEntity(...), $objects);
    }

    /**
     * @param int[] $ids
     */
    public function getByIds(array $ids): array
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('e')
            ->select('e')
            ->where('e.id IN (:ids)')
            ->setParameter('ids', $ids)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    private function fullRelationsQuery(): QueryBuilder
    {
        return $this->entityRepository->createQueryBuilder('e')
            ->select('e')
        ;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function executeFilterRelations(array $filters, QueryBuilder $queryBuilderDistinct): QueryBuilder
    {
        foreach ($filters as $key => $value) {
            if ('many' !== $key) { // one to many and specific values
                $queryBuilderDistinct = $queryBuilderDistinct
                    ->andWhere('e.'.$key.' = :'.$key)
                    ->setParameter($key, $value)
                ;
            } else {
                foreach ($filters['many'] as $many => $manyValue) {
                    $queryBuilderDistinct = $queryBuilderDistinct
                        ->andWhere($many.' IN(:'.$many.')')
                        ->setParameter($many, $manyValue)
                    ;
                }
            }
        }

        return $queryBuilderDistinct;
    }

    private function executePagination(?int $limit, ?int $offset, QueryBuilder $queryBuilderDistinct): QueryBuilder
    {
        if (null !== $limit && 0 !== $limit) {
            $queryBuilderDistinct = $queryBuilderDistinct->setMaxResults($limit);
        }

        if (null !== $offset && 0 !== $offset) {
            $queryBuilderDistinct = $queryBuilderDistinct->setFirstResult($offset);
        }

        return $queryBuilderDistinct;
    }

    /**
     * @param array<string, mixed> $object
     *
     * @return FoodFlatten array
     */
    private function flattenEntity(array $object): array
    {
        $result['id'] = $object['id'];
        $result['name'] = $object['name'];
        $result['type'] = $object['type']->value;
        $result['quantity'] = $object['quantity'];
        $result['unit'] = $object['unit'];

        return $result;
    }
}

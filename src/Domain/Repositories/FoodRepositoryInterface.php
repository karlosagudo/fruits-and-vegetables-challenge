<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Models\Food;

/**
 * @phpstan-type FoodFlatten array{
 *        id: int,
 *        name: string,
 *        type: string,
 *        quantity: int,
 *        unit: string,
 * }
 */
interface FoodRepositoryInterface
{
    public function save(Food $food): void;

    public function persist(Food $food): void;

    public function flush(): void;

    /**
     * @return ($returnEntity is true ? null|Food: FoodFlatten[])
     */
    public function find(int $id, bool $returnEntity = false): null|array|Food;

    public function delete(Food $food): void;

    /**
     * @param array<string, mixed>      $filters
     * @param null|array<string,string> $order
     *
     * @return FoodFlatten[] array
     */
    public function list(array $filters, ?array $order, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param int[] $ids
     *
     * @return Food[]
     */
    public function getByIds(array $ids): array;
}

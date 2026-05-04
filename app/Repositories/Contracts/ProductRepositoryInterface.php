<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    /** @return Collection<int, Product> */
    public function findAllByIds(array $ids): Collection;

    public function lockForUpdate(int $id): ?Product;
}

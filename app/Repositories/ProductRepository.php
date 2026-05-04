<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findAllByIds(array $ids): Collection
    {
        return Product::whereIn('id', $ids)->get();
    }

    public function lockForUpdate(int $id): ?Product
    {
        return Product::where('id', $id)->lockForUpdate()->first();
    }
}

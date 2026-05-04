<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function checkAvailability(int $productId, int $qty): bool
    {
        $product = $this->products->findById($productId);

        return $product !== null && $product->stock_quantity >= $qty;
    }

    /** Must be called inside a DB transaction. Uses SELECT FOR UPDATE to prevent overselling. */
    public function reserveStock(int $productId, int $qty): void
    {
        $product = $this->products->lockForUpdate($productId);

        if ($product === null) {
            throw new RuntimeException("Product #{$productId} not found.");
        }

        if ($product->stock_quantity < $qty) {
            throw new RuntimeException("Insufficient stock for product #{$productId}.");
        }

        DB::table('products')
            ->where('id', $productId)
            ->decrement('stock_quantity', $qty);
    }

    public function releaseStock(int $productId, int $qty): void
    {
        DB::table('products')
            ->where('id', $productId)
            ->increment('stock_quantity', $qty);
    }
}

<?php

namespace App\Services;

use App\DTOs\OrderItemDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use InvalidArgumentException;

class CartService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    /**
     * Validates that all products exist and have sufficient stock.
     * Returns enriched items with price_snapshot and the order total.
     *
     * @param  OrderItemDTO[]  $items
     * @return array{items: array<array{product_id: int, quantity: int, price_snapshot: string}>, total: string}
     */
    public function validate(array $items): array
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Order must contain at least one item.');
        }

        $ids      = array_map(fn(OrderItemDTO $dto) => $dto->productId, $items);
        $products = $this->products->findAllByIds($ids)->keyBy('id');

        $enriched = [];
        $total    = '0.00';

        foreach ($items as $dto) {
            $product = $products->get($dto->productId);

            if ($product === null) {
                throw new InvalidArgumentException("Product #{$dto->productId} does not exist.");
            }

            if ($product->stock_quantity < $dto->quantity) {
                throw new InvalidArgumentException(
                    "Insufficient stock for product #{$dto->productId}. "
                    . "Requested: {$dto->quantity}, available: {$product->stock_quantity}."
                );
            }

            $lineTotal = bcmul((string) $product->price, (string) $dto->quantity, 2);
            $total     = bcadd($total, $lineTotal, 2);

            $enriched[] = [
                'product_id'     => $dto->productId,
                'quantity'       => $dto->quantity,
                'price_snapshot' => $product->price,
            ];
        }

        return ['items' => $enriched, 'total' => $total];
    }
}

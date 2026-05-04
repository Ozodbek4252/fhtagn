<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        return Order::with(['items', 'delivery', 'payment'])->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }
}

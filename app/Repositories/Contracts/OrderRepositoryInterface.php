<?php

namespace App\Repositories\Contracts;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;

    public function create(array $data): Order;
}

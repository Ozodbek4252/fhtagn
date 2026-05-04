<?php

namespace App\Repositories\Contracts;

use App\Models\PickupPoint;

interface PickupPointRepositoryInterface
{
    public function findById(int $id): ?PickupPoint;

    public function findActive(int $id): ?PickupPoint;
}

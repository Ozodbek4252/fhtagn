<?php

namespace App\Repositories;

use App\Models\PickupPoint;
use App\Repositories\Contracts\PickupPointRepositoryInterface;

class PickupPointRepository implements PickupPointRepositoryInterface
{
    public function findById(int $id): ?PickupPoint
    {
        return PickupPoint::find($id);
    }

    public function findActive(int $id): ?PickupPoint
    {
        return PickupPoint::where('id', $id)->where('is_active', true)->first();
    }
}

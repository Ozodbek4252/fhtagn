<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'address', 'is_active'])]
class PickupPoint extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}

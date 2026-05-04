<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'price', 'stock_quantity'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price'          => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }
}

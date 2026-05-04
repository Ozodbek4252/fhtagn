<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'type', 'status', 'amount', 'credit_provider', 'credit_term_months'])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'amount'             => 'decimal:2',
            'credit_term_months' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

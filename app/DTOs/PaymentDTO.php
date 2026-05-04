<?php

namespace App\DTOs;

readonly class PaymentDTO
{
    public function __construct(
        public string $type,
        public ?string $creditProvider = null,
        public ?int $creditTermMonths = null,
    ) {}
}

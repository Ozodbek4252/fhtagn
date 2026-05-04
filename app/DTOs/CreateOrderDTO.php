<?php

namespace App\DTOs;

readonly class CreateOrderDTO
{
    /**
     * @param  OrderItemDTO[]  $items
     */
    public function __construct(
        public int $userId,
        public string $phone,
        public string $email,
        public array $items,
        public DeliveryDTO $delivery,
        public PaymentDTO $payment,
    ) {}
}

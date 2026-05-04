<?php

namespace App\DTOs;

readonly class DeliveryDTO
{
    public function __construct(
        public string $type,
        public ?int $pickupPointId = null,
        public ?string $city = null,
        public ?string $street = null,
        public ?string $building = null,
        public ?string $apartment = null,
    ) {}
}

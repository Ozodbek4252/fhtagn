<?php

namespace App\Services;

use App\DTOs\DeliveryDTO;
use App\Models\Delivery;
use App\Models\Order;
use App\Repositories\Contracts\PickupPointRepositoryInterface;
use InvalidArgumentException;

class DeliveryService
{
    public function __construct(
        private readonly PickupPointRepositoryInterface $pickupPoints,
    ) {}

    public function validate(DeliveryDTO $dto): void
    {
        match ($dto->type) {
            'pickup'  => $this->validatePickup($dto),
            'address' => $this->validateAddress($dto),
            default   => throw new InvalidArgumentException("Unknown delivery type: {$dto->type}."),
        };
    }

    public function createForOrder(Order $order, DeliveryDTO $dto): Delivery
    {
        $this->validate($dto);

        return Delivery::create([
            'order_id'        => $order->id,
            'type'            => $dto->type,
            'pickup_point_id' => $dto->pickupPointId,
            'city'            => $dto->city,
            'street'          => $dto->street,
            'building'        => $dto->building,
            'apartment'       => $dto->apartment,
        ]);
    }

    private function validatePickup(DeliveryDTO $dto): void
    {
        if ($dto->pickupPointId === null) {
            throw new InvalidArgumentException('pickup_point_id is required for pickup delivery.');
        }

        $point = $this->pickupPoints->findActive($dto->pickupPointId);

        if ($point === null) {
            throw new InvalidArgumentException("Pickup point #{$dto->pickupPointId} not found or inactive.");
        }
    }

    private function validateAddress(DeliveryDTO $dto): void
    {
        $missing = array_filter(['city' => $dto->city, 'street' => $dto->street, 'building' => $dto->building],
            fn($v) => $v === null || $v === ''
        );

        if (!empty($missing)) {
            throw new InvalidArgumentException(
                'Missing required address fields: ' . implode(', ', array_keys($missing)) . '.'
            );
        }
    }
}

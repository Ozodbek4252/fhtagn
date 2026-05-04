<?php

namespace App\Http\Requests;

use App\DTOs\CreateOrderDTO;
use App\DTOs\DeliveryDTO;
use App\DTOs\OrderItemDTO;
use App\DTOs\PaymentDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'                    => ['required', 'integer'],
            'phone'                      => ['required', 'string', 'max:20'],
            'email'                      => ['required', 'email'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'integer'],
            'items.*.quantity'           => ['required', 'integer', 'min:1'],
            'delivery'                   => ['required', 'array'],
            'delivery.type'              => ['required', 'string', 'in:pickup,address'],
            'delivery.pickup_point_id'   => ['nullable', 'integer'],
            'delivery.city'              => ['nullable', 'string'],
            'delivery.street'            => ['nullable', 'string'],
            'delivery.building'          => ['nullable', 'string'],
            'delivery.apartment'         => ['nullable', 'string'],
            'payment'                    => ['required', 'array'],
            'payment.type'               => ['required', 'string', 'in:card,credit'],
            'payment.credit_provider'    => ['nullable', 'string'],
            'payment.credit_term_months' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toDTO(): CreateOrderDTO
    {
        $data = $this->validated();

        return new CreateOrderDTO(
            userId: $data['user_id'],
            phone: $data['phone'],
            email: $data['email'],
            items: array_map(
                fn($item) => new OrderItemDTO($item['product_id'], $item['quantity']),
                $data['items']
            ),
            delivery: new DeliveryDTO(
                type: $data['delivery']['type'],
                pickupPointId: $data['delivery']['pickup_point_id'] ?? null,
                city: $data['delivery']['city'] ?? null,
                street: $data['delivery']['street'] ?? null,
                building: $data['delivery']['building'] ?? null,
                apartment: $data['delivery']['apartment'] ?? null,
            ),
            payment: new PaymentDTO(
                type: $data['payment']['type'],
                creditProvider: $data['payment']['credit_provider'] ?? null,
                creditTermMonths: $data['payment']['credit_term_months'] ?? null,
            ),
        );
    }
}

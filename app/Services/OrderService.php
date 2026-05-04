<?php

namespace App\Services;

use App\DTOs\CreateOrderDTO;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly CartService $cart,
        private readonly StockService $stock,
        private readonly DeliveryService $delivery,
        private readonly PaymentService $payment,
    ) {}

    public function createOrder(CreateOrderDTO $dto): Order
    {
        if (!$this->users->exists($dto->userId)) {
            throw new InvalidArgumentException("User #{$dto->userId} does not exist.");
        }

        // Validate all inputs before opening a transaction
        $cartResult = $this->cart->validate($dto->items);
        $this->delivery->validate($dto->delivery);
        $this->payment->validate($dto->payment);

        $order = DB::transaction(function () use ($dto, $cartResult): Order {
            $order = Order::create([
                'user_id'      => $dto->userId,
                'phone'        => $dto->phone,
                'email'        => $dto->email,
                'status'       => 'pending',
                'total_amount' => $cartResult['total'],
            ]);

            foreach ($cartResult['items'] as $item) {
                // Re-check stock with row lock inside the transaction
                $this->stock->reserveStock($item['product_id'], $item['quantity']);

                OrderItem::create([
                    'order_id'       => $order->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'price_snapshot' => $item['price_snapshot'],
                ]);
            }

            $this->delivery->createForOrder($order, $dto->delivery);
            $this->payment->createForOrder($order, $dto->payment);

            return $order;
        });

        OrderCreated::dispatch($order);

        return $order;
    }
}

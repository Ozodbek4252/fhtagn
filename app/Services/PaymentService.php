<?php

namespace App\Services;

use App\DTOs\PaymentDTO;
use App\Models\Order;
use App\Models\Payment;
use InvalidArgumentException;

class PaymentService
{
    public function validate(PaymentDTO $dto): void
    {
        match ($dto->type) {
            'card'   => null,
            'credit' => $this->validateCredit($dto),
            default  => throw new InvalidArgumentException("Unknown payment type: {$dto->type}."),
        };
    }

    public function createForOrder(Order $order, PaymentDTO $dto): Payment
    {
        $this->validate($dto);

        return Payment::create([
            'order_id'           => $order->id,
            'type'               => $dto->type,
            'status'             => 'pending',
            'amount'             => $order->total_amount,
            'credit_provider'    => $dto->creditProvider,
            'credit_term_months' => $dto->creditTermMonths,
        ]);
    }

    /** Marks payment as paid — called after successful card charge or credit approval. */
    public function markPaid(Payment $payment): void
    {
        $payment->update(['status' => 'paid']);
        $payment->order->update(['status' => 'confirmed']);
    }

    /** Marks payment as failed — called on charge failure or credit rejection. */
    public function markFailed(Payment $payment): void
    {
        $payment->update(['status' => 'failed']);
    }

    private function validateCredit(PaymentDTO $dto): void
    {
        if (empty($dto->creditProvider)) {
            throw new InvalidArgumentException('credit_provider is required for credit payment.');
        }

        if ($dto->creditTermMonths === null || $dto->creditTermMonths < 1) {
            throw new InvalidArgumentException('credit_term_months must be at least 1.');
        }
    }
}

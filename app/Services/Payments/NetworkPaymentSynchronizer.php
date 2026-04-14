<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NetworkPaymentSynchronizer
{
    public function __construct(
        protected PaymentTransactionLogger $activityLogger,
    ) {}

    /**
     * Pull the latest order state from N-Genius and update the local transaction.
     */
    public function synchronizeFromGateway(
        PaymentTransaction $transaction,
        NetworkNgeniusGateway $gateway,
        BookingConfirmationService $confirmationService,
    ): bool {
        if (! $transaction->gateway_order_ref) {
            return false;
        }

        try {
            $payload = $gateway->fetchOrder($transaction->gateway_order_ref);
        } catch (\Throwable $exception) {
            Log::error('N-Genius fetchOrder failed during sync.', [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ]);

            $this->activityLogger->record(
                $transaction,
                'gateway_fetch_failed',
                'Could not fetch order from N-Genius; status left unchanged.',
                ['message' => $exception->getMessage()],
                auth()->user(),
            );

            return false;
        }

        $this->applyOrderPayload($transaction, $payload, $confirmationService);

        return true;
    }

    public function applyOrderPayload(
        PaymentTransaction $transaction,
        array $payload,
        BookingConfirmationService $confirmationService,
    ): void {
        $paymentState = $this->resolveGatewayState($payload);
        $status = $this->mapGatewayStateToStatus($paymentState);
        $statusBefore = $transaction->status;

        $transaction->update([
            'status' => $status,
            'gateway_payment_ref' => $this->resolvePaymentReference($payload),
            'gateway_payload' => $payload,
            'paid_at' => in_array($status, ['paid', 'authorized'], true) ? now() : $transaction->paid_at,
        ]);

        $transaction->refresh();

        $this->activityLogger->record(
            $transaction,
            'gateway_order_synchronized',
            'Order payload applied from N-Genius (callback, webhook, or manual refresh).',
            [
                'gateway_payment_state' => $paymentState,
                'status_unchanged' => $statusBefore === $status,
            ],
            auth()->user(),
        );

        if (in_array($status, ['paid', 'authorized'], true)) {
            $confirmationService->send($transaction->fresh());
        }
    }

    protected function resolveGatewayState(array $payload): string
    {
        return Str::upper(
            Arr::get($payload, '_embedded.payment.0.state')
            ?? Arr::get($payload, 'state')
            ?? 'PENDING'
        );
    }

    protected function resolvePaymentReference(array $payload): ?string
    {
        $id = Arr::get($payload, '_embedded.payment.0._id');

        return $id ? Str::afterLast($id, ':') : null;
    }

    protected function mapGatewayStateToStatus(string $state): string
    {
        return match ($state) {
            'PURCHASED', 'CAPTURED' => 'paid',
            'AUTHORIZED' => 'authorized',
            'FAILED', 'DECLINED' => 'failed',
            'CANCELLED', 'CANCELED' => 'cancelled',
            default => 'pending',
        };
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Services\Payments\BookingConfirmationService;
use App\Services\Payments\NetworkNgeniusGateway;
use App\Services\Payments\NetworkPaymentSynchronizer;
use App\Services\Payments\PaymentTransactionLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NetworkWebhookController extends Controller
{
    /**
     * N-Genius / Network International outbound webhook.
     *
     * Register this URL in the Network merchant portal. We verify a shared secret, then
     * re-fetch the order from the API so status is authoritative (same as browser return URL).
     *
     * @see https://developer.network.global/apis/webhook
     */
    public function __invoke(
        Request $request,
        NetworkNgeniusGateway $gateway,
        BookingConfirmationService $confirmationService,
        NetworkPaymentSynchronizer $synchronizer,
        PaymentTransactionLogger $activityLogger,
    ): JsonResponse {
        $this->assertAuthorized($request);

        $payload = $request->json()->all();
        if ($payload === []) {
            $decoded = json_decode($request->getContent(), true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        $transaction = $this->resolveTransaction($payload);

        if (! $transaction) {
            Log::info('Network payment webhook: no matching transaction.', [
                'top_level_keys' => array_keys($payload),
            ]);

            return response()->json(['received' => true, 'matched' => false]);
        }

        if (! $transaction->gateway_order_ref) {
            return response()->json([
                'received' => true,
                'matched' => true,
                'skipped' => 'missing_gateway_order_ref',
            ]);
        }

        $ok = $synchronizer->synchronizeFromGateway($transaction, $gateway, $confirmationService);

        if (! $ok) {
            Log::error('network_payment_webhook.sync_failed', [
                'transaction_id' => $transaction->id,
                'gateway_order_ref' => $transaction->gateway_order_ref,
            ]);

            return response()->json(['received' => true, 'matched' => true, 'synced' => false], 500);
        }

        $activityLogger->record(
            $transaction->fresh(),
            'payment_webhook_handled',
            'Network outbound webhook received; order re-fetched and synchronized.',
            [],
            null,
        );

        return response()->json(['received' => true, 'matched' => true, 'synced' => true]);
    }

    protected function assertAuthorized(Request $request): void
    {
        $secret = config('payments.network.webhook_secret');

        if ($secret === null || $secret === '') {
            if (app()->environment('production')) {
                throw new HttpException(503, 'Webhook secret not configured.');
            }

            return;
        }

        $provided = $request->header('X-Network-Webhook-Secret')
            ?? $request->header('X-Webhook-Secret');

        if (! is_string($provided) || ! hash_equals($secret, $provided)) {
            throw new HttpException(401, 'Unauthorized.');
        }
    }

    protected function resolveTransaction(array $payload): ?PaymentTransaction
    {
        foreach ($this->referenceCandidates($payload) as $ref) {
            if (! is_string($ref) || $ref === '') {
                continue;
            }

            $transaction = PaymentTransaction::query()
                ->where(function ($query) use ($ref): void {
                    $query->where('gateway_order_ref', $ref)
                        ->orWhere('reference', $ref);
                })
                ->first();

            if ($transaction) {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected function referenceCandidates(array $payload): array
    {
        $out = [];

        $walk = function (mixed $node) use (&$out, &$walk): void {
            if (! is_array($node)) {
                return;
            }

            foreach ($node as $key => $value) {
                if (is_string($key) && in_array($key, [
                    'reference',
                    'orderReference',
                    'merchantOrderReference',
                    'order_ref',
                    'orderRef',
                ], true) && is_string($value) && $value !== '') {
                    $out[] = $value;
                }

                if (is_array($value)) {
                    $walk($value);
                }
            }
        };

        $walk($payload);

        return array_values(array_unique($out));
    }
}

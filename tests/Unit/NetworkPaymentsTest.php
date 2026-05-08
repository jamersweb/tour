<?php

namespace Tests\Unit;

use App\Support\NetworkPayments;
use Tests\TestCase;

class NetworkPaymentsTest extends TestCase
{
    public function test_checkout_ready_when_api_secret_omitted_but_api_key_is_base64_blob(): void
    {
        $blob = base64_encode('11111111-1111-1111-1111-111111111111:22222222-2222-2222-2222-222222222222');

        config([
            'payments.network.enabled' => true,
            'payments.network.outlet_id' => 'test-outlet',
            'payments.network.api_key' => $blob,
            'payments.network.api_secret' => '',
        ]);

        $this->assertTrue(NetworkPayments::isCheckoutReady());
    }

    public function test_checkout_ready_when_raw_key_colon_secret_in_api_key_only(): void
    {
        config([
            'payments.network.enabled' => true,
            'payments.network.outlet_id' => 'test-outlet',
            'payments.network.api_key' => 'key-uuid:secret-uuid',
            'payments.network.api_secret' => '',
        ]);

        $this->assertTrue(NetworkPayments::isCheckoutReady());
    }
}

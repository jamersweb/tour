<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Match seeded admin so staff alerts are not duplicated (operations inbox vs. admin users).
        config(['mail.bookings.notify_address' => 'admin@acutetourism.org']);
    }

    /**
     * Satisfies NetworkPayments::isCheckoutReady() for tests that hit checkout pages.
     */
    protected function enableNetworkCheckoutForTests(): void
    {
        config([
            'payments.network.enabled' => true,
            'payments.network.outlet_id' => 'test-outlet',
            'payments.network.api_key' => 'test-api-key',
            'payments.network.api_secret' => 'test-api-secret',
        ]);
    }
}

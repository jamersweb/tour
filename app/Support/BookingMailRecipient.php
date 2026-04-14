<?php

namespace App\Support;

use App\Models\SiteSetting;

class BookingMailRecipient
{
    /**
     * Operations inbox for staff alerts (BOOKING_NOTIFICATIONS_EMAIL or site contact email).
     */
    public static function operationsEmail(): ?string
    {
        $configured = config('mail.bookings.notify_address');

        if (is_string($configured) && filter_var(trim($configured), FILTER_VALIDATE_EMAIL)) {
            return trim($configured);
        }

        $fallback = SiteSetting::current()->contact_email;

        if (is_string($fallback) && filter_var(trim($fallback), FILTER_VALIDATE_EMAIL)) {
            return trim($fallback);
        }

        return null;
    }
}

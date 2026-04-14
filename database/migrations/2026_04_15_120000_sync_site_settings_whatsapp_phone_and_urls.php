<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures public WhatsApp (wa.me), displayed phone, and footer "Official Website" / social
 * URLs match current Acute Tourism contacts. Safe to re-run on id=1 only.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        if (! DB::table('site_settings')->where('id', 1)->exists()) {
            return;
        }

        DB::table('site_settings')->where('id', 1)->update([
            /** Stored with formatting; frontend strips non-digits for wa.me */
            'whatsapp_number' => '+971 58 516 1554',
            'contact_phone' => '(+971) 58 516 1554',
            'contact_phone_secondary' => null,
            'website_url' => 'https://new.acutetourism.org',
            'social_links' => json_encode([
                'https://www.instagram.com/acutetourism',
                'https://www.facebook.com/acutetourism',
            ]),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Intentionally empty: do not restore obsolete numbers on rollback.
    }
};

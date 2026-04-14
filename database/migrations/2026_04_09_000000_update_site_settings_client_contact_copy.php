<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const FOOTER_COPY = 'Exclusively Curated Holiday Experiences, crafted by destination experts, with itinerary designed to deliver a refined and effortless journey with luxury hotels, private transfers, bespoke desert safaris, and priority access to iconic landmarks.';

    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'contact_phone_secondary')) {
                $table->string('contact_phone_secondary', 100)->nullable()->after('contact_phone');
            }
        });

        DB::table('site_settings')->where('id', 1)->update([
            'footer_description' => self::FOOTER_COPY,
            'contact_phone' => '(+971) 58 516 1554',
            'contact_phone_secondary' => null,
            'contact_address' => 'Shop 10, Kempinski Hotel & Residences, Palm Jumeirah, Dubai',
            'whatsapp_number' => '+971 58 516 1554',
            'website_url' => 'https://new.acutetourism.org',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        DB::table('site_settings')->where('id', 1)->update([
            'footer_description' => 'Premium Dubai experiences being rebuilt with a focused product architecture, stronger branding, and a concierge-first booking flow.',
            'contact_phone' => '(+971) 4 409 6751',
            'contact_address' => 'Emaar Square, Boulevard Plaza Tower 2, Dubai',
            'whatsapp_number' => '+97144096751',
            'updated_at' => now(),
        ]);

        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'contact_phone_secondary')) {
                $table->dropColumn('contact_phone_secondary');
            }
        });
    }
};

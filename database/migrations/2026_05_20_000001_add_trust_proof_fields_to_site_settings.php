<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('license_number')->nullable()->after('whatsapp_number');
            $table->string('google_reviews_url')->nullable()->after('license_number');
            $table->string('tripadvisor_reviews_url')->nullable()->after('google_reviews_url');
            $table->json('partner_proof')->nullable()->after('tripadvisor_reviews_url');
        });

        DB::table('site_settings')
            ->whereNull('tripadvisor_reviews_url')
            ->update([
                'tripadvisor_reviews_url' => 'https://www.tripadvisor.com/Attraction_Review-g295424-d33025321-Reviews-Acute_Tourism_LLC-Dubai_Emirate_of_Dubai.html',
                'partner_proof' => json_encode([
                    'Secure checkout via Network payment gateway',
                    'Office address at Kempinski Hotel & Residences, Palm Jumeirah',
                    'Hotels, transport, attractions, and visa-document support coordinated through one Acute team',
                ]),
            ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'license_number',
                'google_reviews_url',
                'tripadvisor_reviews_url',
                'partner_proof',
            ]);
        });
    }
};

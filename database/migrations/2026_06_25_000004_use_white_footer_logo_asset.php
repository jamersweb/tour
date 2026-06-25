<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const WHITE_LOGO = '/images/acute-tourism-logo-white.webp';

    private const REPLACEABLE_FOOTER_LOGOS = [
        '/images/acute-tourism-logo.png',
        '/images/acute-tourism-logo.svg',
        '/legacy_media/uploads/0000/6/2025/03/14/4-2.png',
        '/legacy-media/uploads/0000/6/2025/03/14/4-2.png',
        'https://acutetourism.org/uploads/0000/6/2025/03/14/4-2.png',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('site_settings') || ! Schema::hasColumn('site_settings', 'footer_logo_url')) {
            return;
        }

        DB::table('site_settings')
            ->whereNull('footer_logo_url')
            ->orWhereIn('footer_logo_url', self::REPLACEABLE_FOOTER_LOGOS)
            ->update([
                'footer_logo_url' => self::WHITE_LOGO,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Do not restore old footer logo URLs.
    }
};

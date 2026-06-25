<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LOCAL_LOGO = '/images/acute-tourism-logo.png';

    private const DEAD_LOGOS = [
        '/images/acute-tourism-logo.svg',
        '/legacy_media/uploads/0000/6/2025/03/19/5.png',
        '/legacy-media/uploads/0000/6/2025/03/19/5.png',
        '/legacy_media/uploads/0000/6/2025/03/14/4-2.png',
        '/legacy-media/uploads/0000/6/2025/03/14/4-2.png',
        'https://acutetourism.org/uploads/0000/6/2025/03/19/5.png',
        'https://acutetourism.org/uploads/0000/6/2025/03/14/4-2.png',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        foreach (['logo_url', 'footer_logo_url'] as $column) {
            if (! Schema::hasColumn('site_settings', $column)) {
                continue;
            }

            DB::table('site_settings')
                ->whereIn($column, self::DEAD_LOGOS)
                ->update([
                    $column => self::LOCAL_LOGO,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Do not restore dead legacy logo URLs.
    }
};

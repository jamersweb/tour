<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MEDIA_REPLACEMENTS = [
        'https://new.acutetourism.org/uploads/' => '',
        'http://new.acutetourism.org/uploads/' => '',
        'https:\/\/new.acutetourism.org\/uploads\/' => '',
        'http:\/\/new.acutetourism.org\/uploads\/' => '',
        'https://acutetourism.ae/uploads/' => '',
        'http://acutetourism.ae/uploads/' => '',
        'https:\/\/acutetourism.ae\/uploads\/' => '',
        'http:\/\/acutetourism.ae\/uploads\/' => '',
        'https://www.acutetourism.ae/uploads/' => '',
        'http://www.acutetourism.ae/uploads/' => '',
        'https:\/\/www.acutetourism.ae\/uploads\/' => '',
        'http:\/\/www.acutetourism.ae\/uploads\/' => '',
    ];

    private const TEXT_REPLACEMENTS = [
        '971585161554' => '971521926984',
        '+971585161554' => '+971521926984',
        '+971 58 516 1554' => '+971 52 192 6984',
        '(+971) 58 516 1554' => '(+971) 52 192 6984',
        '971 58 516 1554' => '971 52 192 6984',
        '58 516 1554' => '52 192 6984',
    ];

    private const CONTENT_TABLES = [
        'site_settings',
        'packages',
        'tours',
        'experiences',
        'collections',
        'articles',
    ];

    public function up(): void
    {
        $this->syncSiteSettings();
        $this->normalizeContentTables();
    }

    public function down(): void
    {
        // Do not restore obsolete public contact details or media URLs.
    }

    private function syncSiteSettings(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        if (! DB::table('site_settings')->where('id', 1)->exists()) {
            return;
        }

        DB::table('site_settings')->where('id', 1)->update([
            'website_url' => 'https://acutetourism.ae',
            'logo_url' => '/legacy-media/uploads/0000/6/2025/03/19/5.png',
            'footer_logo_url' => '/legacy-media/uploads/0000/6/2025/03/14/4-2.png',
            'contact_phone' => '(+971) 52 192 6984',
            'contact_phone_secondary' => null,
            'whatsapp_number' => '+971 52 192 6984',
            'updated_at' => now(),
        ]);
    }

    private function normalizeContentTables(): void
    {
        foreach (self::CONTENT_TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            DB::table($table)
                ->orderBy('id')
                ->select('*')
                ->cursor()
                ->each(function (object $row) use ($table): void {
                    $updates = [];

                    foreach ((array) $row as $column => $value) {
                        if (! is_string($value)) {
                            continue;
                        }

                        $normalized = str_replace(
                            array_keys(self::MEDIA_REPLACEMENTS),
                            array_values(self::MEDIA_REPLACEMENTS),
                            $value,
                        );
                        $normalized = str_replace(
                            array_keys(self::TEXT_REPLACEMENTS),
                            array_values(self::TEXT_REPLACEMENTS),
                            $normalized,
                        );

                        if ($normalized !== $value) {
                            $updates[$column] = $normalized;
                        }
                    }

                    if ($updates !== []) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                });
        }
    }
};

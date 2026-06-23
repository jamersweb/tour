<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_DOMAIN = 'https://new.acutetourism.org';

    private const NEW_DOMAIN = 'https://acutetourism.ae';

    private const TABLES = [
        'site_settings',
        'packages',
        'tours',
        'experiences',
        'collections',
        'articles',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
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
                        if (! is_string($value) || ! str_contains($value, self::OLD_DOMAIN)) {
                            continue;
                        }

                        $updates[$column] = str_replace(self::OLD_DOMAIN, self::NEW_DOMAIN, $value);
                    }

                    if ($updates !== []) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                });
        }
    }

    public function down(): void
    {
        // Intentionally empty: do not restore obsolete media URLs on rollback.
    }
};

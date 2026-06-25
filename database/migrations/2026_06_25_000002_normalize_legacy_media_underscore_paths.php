<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
                        if (! is_string($value) || ! str_contains($value, '/legacy_media/uploads/')) {
                            continue;
                        }

                        $updates[$column] = str_replace(
                            '/legacy_media/uploads/',
                            '/legacy-media/uploads/',
                            $value,
                        );
                    }

                    if ($updates !== []) {
                        DB::table($table)->where('id', $row->id)->update($updates);
                    }
                });
        }
    }

    public function down(): void
    {
        // Do not restore broken legacy_media URLs.
    }
};

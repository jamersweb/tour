<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DOMAINS = [
        'https://new.acutetourism.org/uploads/',
        'https://acutetourism.ae/uploads/',
        'https://www.acutetourism.ae/uploads/',
    ];

    private const TABLES = [
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
                        if (! is_string($value) || ! str_contains($value, '/uploads/')) {
                            continue;
                        }

                        $normalized = str_replace(self::DOMAINS, '', $value);

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

    public function down(): void
    {
        // Intentionally empty: public uploads should stay stored as disk-relative paths.
    }
};

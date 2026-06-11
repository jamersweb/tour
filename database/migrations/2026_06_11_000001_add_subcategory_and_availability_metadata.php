<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            if (! Schema::hasColumn('collections', 'collection_group')) {
                $table->string('collection_group', 40)->default('activity')->after('slug');
            }
        });

        foreach (['experiences', 'tours'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'unavailable_dates')) {
                    $table->json('unavailable_dates')->nullable()->after('booking_options');
                }

                if (! Schema::hasColumn($tableName, 'unavailable_periods')) {
                    $table->json('unavailable_periods')->nullable()->after('unavailable_dates');
                }
            });
        }

        DB::table('collections')
            ->orderBy('id')
            ->get(['id', 'name', 'slug'])
            ->each(function ($collection): void {
                $text = Str::of($collection->slug.' '.$collection->name)->lower()->toString();
                $group = str_contains($text, 'dubai')
                    || str_contains($text, 'abu-dhabi')
                    || str_contains($text, 'abu dhabi')
                    || str_contains($text, 'emirate')
                    ? 'location'
                    : 'activity';

                DB::table('collections')->where('id', $collection->id)->update([
                    'collection_group' => $group,
                ]);
            });
    }

    public function down(): void
    {
        foreach (['tours', 'experiences'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'unavailable_periods')) {
                    $table->dropColumn('unavailable_periods');
                }

                if (Schema::hasColumn($tableName, 'unavailable_dates')) {
                    $table->dropColumn('unavailable_dates');
                }
            });
        }

        Schema::table('collections', function (Blueprint $table) {
            if (Schema::hasColumn('collections', 'collection_group')) {
                $table->dropColumn('collection_group');
            }
        });
    }
};

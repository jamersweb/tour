<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('experiences', 'child_price_from')) {
                $table->decimal('child_price_from', 10, 2)->nullable()->after('price_from');
            }
        });

        Schema::table('tours', function (Blueprint $table) {
            if (! Schema::hasColumn('tours', 'child_price_from')) {
                $table->decimal('child_price_from', 10, 2)->nullable()->after('price_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'child_price_from')) {
                $table->dropColumn('child_price_from');
            }
        });

        Schema::table('experiences', function (Blueprint $table) {
            if (Schema::hasColumn('experiences', 'child_price_from')) {
                $table->dropColumn('child_price_from');
            }
        });
    }
};

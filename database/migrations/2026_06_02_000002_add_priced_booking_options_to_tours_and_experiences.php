<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('experiences', 'booking_options')) {
                $table->json('booking_options')->nullable()->after('tour_options');
            }
        });

        Schema::table('tours', function (Blueprint $table) {
            if (! Schema::hasColumn('tours', 'booking_options')) {
                $table->json('booking_options')->nullable()->after('tour_options');
            }
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('booking_options');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('booking_options');
        });
    }
};

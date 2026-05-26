<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->json('preferred_time_options')->nullable()->after('cancellation_policy');
            $table->json('preferred_language_options')->nullable()->after('preferred_time_options');
            $table->json('tour_options')->nullable()->after('preferred_language_options');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->json('preferred_time_options')->nullable()->after('cancellation_policy');
            $table->json('preferred_language_options')->nullable()->after('preferred_time_options');
            $table->json('tour_options')->nullable()->after('preferred_language_options');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_time_options',
                'preferred_language_options',
                'tour_options',
            ]);
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_time_options',
                'preferred_language_options',
                'tour_options',
            ]);
        });
    }
};

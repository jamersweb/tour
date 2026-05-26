<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->json('important_notices')->nullable()->after('exclusions');
            $table->json('expectation_steps')->nullable()->after('important_notices');
            $table->json('best_for')->nullable()->after('expectation_steps');
            $table->json('faqs')->nullable()->after('best_for');
            $table->text('cancellation_policy')->nullable()->after('faqs');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->json('important_notices')->nullable()->after('exclusions');
            $table->json('expectation_steps')->nullable()->after('important_notices');
            $table->json('best_for')->nullable()->after('expectation_steps');
            $table->json('faqs')->nullable()->after('best_for');
            $table->text('cancellation_policy')->nullable()->after('faqs');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'important_notices',
                'expectation_steps',
                'best_for',
                'faqs',
                'cancellation_policy',
            ]);
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'important_notices',
                'expectation_steps',
                'best_for',
                'faqs',
                'cancellation_policy',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->json('important_notices')->nullable()->after('exclusions');
            $table->json('best_for')->nullable()->after('important_notices');
            $table->text('cancellation_policy')->nullable()->after('best_for');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'important_notices',
                'best_for',
                'cancellation_policy',
            ]);
        });
    }
};

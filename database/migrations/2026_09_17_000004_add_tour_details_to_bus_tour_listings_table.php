<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_tour_listings', function (Blueprint $table): void {
            $table->text('tour_details')->nullable()->after('short_description');
        });
    }

    public function down(): void
    {
        Schema::table('bus_tour_listings', function (Blueprint $table): void {
            $table->dropColumn('tour_details');
        });
    }
};

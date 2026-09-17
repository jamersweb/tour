<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_tour_pages', function (Blueprint $table): void {
            $table->string('private_image_path')->nullable()->after('private_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('bus_tour_pages', function (Blueprint $table): void {
            $table->dropColumn('private_image_path');
        });
    }
};

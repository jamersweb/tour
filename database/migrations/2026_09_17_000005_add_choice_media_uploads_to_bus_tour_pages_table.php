<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bus_tour_pages', function (Blueprint $table): void {
            $table->string('choice_media_image_path')->nullable()->after('choice_media_copy');
            $table->string('choice_media_image_url')->nullable()->after('choice_media_image_path');
            $table->string('choice_media_video_path')->nullable()->after('choice_media_image_url');
            $table->string('choice_media_video_url')->nullable()->after('choice_media_video_path');
        });
    }

    public function down(): void
    {
        Schema::table('bus_tour_pages', function (Blueprint $table): void {
            $table->dropColumn([
                'choice_media_image_path',
                'choice_media_image_url',
                'choice_media_video_path',
                'choice_media_video_url',
            ]);
        });
    }
};

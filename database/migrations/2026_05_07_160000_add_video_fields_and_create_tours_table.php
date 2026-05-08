<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('hero_video_url')->nullable()->after('hero_image_path');
            $table->json('gallery_videos')->nullable()->after('gallery_images');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('hero_video_url')->nullable()->after('hero_image_path');
            $table->json('gallery_videos')->nullable()->after('gallery_images');
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 80)->nullable();
            $table->string('short_description', 280)->nullable();
            $table->text('description')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->string('hero_video_url')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('gallery_videos')->nullable();
            $table->string('duration', 80)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('pickup_note', 180)->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->string('currency', 3)->default('AED');
            $table->json('highlights')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_private')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['hero_video_url', 'gallery_videos']);
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn(['hero_video_url', 'gallery_videos']);
        });
    }
};

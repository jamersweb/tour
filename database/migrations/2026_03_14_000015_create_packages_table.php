<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 280)->nullable();
            $table->text('description')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('duration', 80)->nullable();
            $table->unsignedInteger('days')->nullable();
            $table->unsignedInteger('nights')->nullable();
            $table->string('location', 120)->nullable();
            $table->unsignedInteger('group_size_min')->nullable();
            $table->unsignedInteger('group_size_max')->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('AED');
            $table->json('highlights')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->json('itinerary')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');
            $table->string('author_name');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('quote');
            $table->string('tag')->nullable();
            $table->string('source')->default('Website');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index(['is_published', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

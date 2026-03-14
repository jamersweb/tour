<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 80);
            $table->string('short_description', 240)->nullable();
            $table->text('description')->nullable();
            $table->string('duration', 80)->nullable();
            $table->string('location', 120)->nullable();
            $table->decimal('price_from', 10, 2)->nullable();
            $table->string('currency', 3)->default('AED');
            $table->string('tag', 40)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_private')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};

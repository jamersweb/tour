<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('travel_date')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->string('interest', 80);
            $table->text('message');
            $table->string('source', 40)->default('website');
            $table->string('status', 40)->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_inquiries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Acute Tourism');
            $table->string('brand_kicker')->default('Acute');
            $table->string('brand_name')->default('Tourism');
            $table->string('site_tagline')->default('Premium Dubai experiences rebuilt on Laravel, Vue, and Inertia.');
            $table->text('footer_description')->nullable();
            $table->json('footer_build_notes')->nullable();
            $table->json('footer_milestones')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->json('interest_options')->nullable();
            $table->string('home_hero_eyebrow')->nullable();
            $table->string('home_hero_title')->nullable();
            $table->text('home_hero_description')->nullable();
            $table->string('home_primary_cta_label')->nullable();
            $table->string('home_secondary_cta_label')->nullable();
            $table->string('home_trust_heading')->nullable();
            $table->json('home_trust_points')->nullable();
            $table->string('home_collections_eyebrow')->nullable();
            $table->string('home_collections_title')->nullable();
            $table->string('home_featured_eyebrow')->nullable();
            $table->string('home_featured_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

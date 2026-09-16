<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_tour_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('hero_eyebrow')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_primary_cta_label')->nullable();
            $table->string('hero_secondary_cta_label')->nullable();
            $table->string('hero_fomo_line')->nullable();
            $table->json('hero_facts')->nullable();
            $table->text('intro_mark')->nullable();
            $table->string('intro_eyebrow')->nullable();
            $table->text('intro_title')->nullable();
            $table->text('intro_copy')->nullable();
            $table->string('choice_media_label')->nullable();
            $table->string('choice_media_title')->nullable();
            $table->text('choice_media_copy')->nullable();
            $table->json('choice_lines')->nullable();
            $table->string('choice_primary_cta_label')->nullable();
            $table->string('choice_secondary_cta_label')->nullable();
            $table->string('routes_eyebrow')->nullable();
            $table->text('routes_title')->nullable();
            $table->text('routes_copy')->nullable();
            $table->json('routes')->nullable();
            $table->text('availability_note')->nullable();
            $table->string('details_eyebrow')->nullable();
            $table->text('details_title')->nullable();
            $table->text('details_copy')->nullable();
            $table->string('before_booking_eyebrow')->nullable();
            $table->text('before_booking_title')->nullable();
            $table->text('before_booking_copy')->nullable();
            $table->json('before_booking_cards')->nullable();
            $table->string('private_eyebrow')->nullable();
            $table->text('private_title')->nullable();
            $table->text('private_copy')->nullable();
            $table->json('private_lines')->nullable();
            $table->string('private_cta_label')->nullable();
            $table->string('private_image_url')->nullable();
            $table->string('audience_eyebrow')->nullable();
            $table->text('audience_title')->nullable();
            $table->text('audience_copy')->nullable();
            $table->json('audiences')->nullable();
            $table->string('gallery_eyebrow')->nullable();
            $table->text('gallery_title')->nullable();
            $table->text('gallery_copy')->nullable();
            $table->json('gallery_items')->nullable();
            $table->text('gallery_note')->nullable();
            $table->string('enquiry_eyebrow')->nullable();
            $table->text('enquiry_title')->nullable();
            $table->text('enquiry_copy')->nullable();
            $table->string('enquiry_submit_label')->nullable();
            $table->string('enquiry_processing_label')->nullable();
            $table->string('enquiry_whatsapp_label')->nullable();
            $table->text('enquiry_note')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->string('faq_eyebrow')->nullable();
            $table->text('faq_title')->nullable();
            $table->json('faqs')->nullable();
            $table->string('sticky_label')->nullable();
            $table->string('sticky_text')->nullable();
            $table->string('sticky_cta_label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_tour_pages');
    }
};

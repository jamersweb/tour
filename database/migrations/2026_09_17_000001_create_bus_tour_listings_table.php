<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_tour_listings', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('route_label')->nullable();
            $table->string('price')->nullable();
            $table->string('panel_price')->nullable();
            $table->string('category_label')->nullable();
            $table->text('short_description')->nullable();
            $table->text('best_for')->nullable();
            $table->string('card_image_path')->nullable();
            $table->string('card_image_url')->nullable();
            $table->string('detail_image_path')->nullable();
            $table->string('detail_image_url')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('gallery_image_urls')->nullable();
            $table->json('tags')->nullable();
            $table->json('highlights')->nullable();
            $table->json('included')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('bus_tour_listings')->insert([
            [
                'title' => 'Dubai Panoramic Bus + Food Tasting',
                'slug' => 'dubai-panoramic-bus-food-tasting',
                'route_label' => 'Dubai route',
                'price' => 'AED 499 per person',
                'panel_price' => 'AED 499 / person',
                'category_label' => 'City, food and sundowner',
                'short_description' => 'A city-focused luxury bus tour with hotel pick-up, Museum of the Future photo stop, Al Seef, DIFC, food tasting, lunch, and a sundowner at The Palm.',
                'best_for' => 'Best for visitors, residents hosting guests, couples and small groups who want a relaxed city route with selected Dubai highlights and food-focused moments.',
                'card_image_url' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1300&q=85',
                'detail_image_url' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1600&q=85',
                'tags' => json_encode(['Food tasting', 'Lunch', 'Guide']),
                'highlights' => json_encode(['Hotel pick-up from selected Dubai areas', 'Museum of the Future photo stop', 'Al Seef photo stop', 'DIFC photo stop', 'Sundowner at The Palm']),
                'included' => json_encode(['Hotel pick-up and drop-off', 'Professional guide', 'Food tasting', 'Lunch', 'Water and soft drinks']),
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Al Ain Panoramic Bus + Al Ain Zoo',
                'slug' => 'al-ain-panoramic-bus-al-ain-zoo',
                'route_label' => 'Al Ain route',
                'price' => 'AED 499 per person',
                'panel_price' => 'AED 499 / person',
                'category_label' => 'Wildlife and heritage',
                'short_description' => 'A family-friendly journey with Al Ain Zoo admission, Jebel Hafeet, Al Jahili Fort, Hili Archaeological Park, lunch, and guide.',
                'best_for' => 'Best for families, residents and guests who want a comfortable wildlife and heritage day outside Dubai with attraction access already included.',
                'card_image_url' => 'https://images.unsplash.com/photo-1551969014-7d2c4cddf0b6?auto=format&fit=crop&w=1300&q=85',
                'detail_image_url' => 'https://images.unsplash.com/photo-1551969014-7d2c4cddf0b6?auto=format&fit=crop&w=1600&q=85',
                'tags' => json_encode(['Zoo ticket', 'Family-friendly', 'Lunch']),
                'highlights' => json_encode(['National Museum photo stop', 'Hili Archaeological Park photo stop', 'Jebel Hafeet', 'Al Jahili Fort', 'Al Ain Zoo visit']),
                'included' => json_encode(['Hotel pick-up and drop-off', 'Al Ain Zoo admission ticket', 'Professional guide', 'Lunch', 'Water and soft drinks']),
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Fujairah Panoramic Bus',
                'slug' => 'fujairah-panoramic-bus',
                'route_label' => 'Fujairah route',
                'price' => 'AED 699 per person',
                'panel_price' => 'AED 699 / person',
                'category_label' => 'Coastal and marine experience',
                'short_description' => 'A coastal route with Friday Market, Al Hayl Castle, Khorfakkan Waterfall, oyster farm visit, beach access, and marine activities.',
                'best_for' => 'Best for guests looking for the most distinctive route: a coastal journey with heritage stops, Khorfakkan scenery, lunch by the beach and marine-focused experiences.',
                'card_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1300&q=85',
                'detail_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1600&q=85',
                'tags' => json_encode(['Oyster farm', 'Beach access', 'Marine experience']),
                'highlights' => json_encode(['Hotel pick-up from selected Dubai areas', 'Friday Market shopping and photo stop', 'Al Hayl Castle', 'Khorfakkan Waterfall', 'Return transfer to your hotel']),
                'included' => json_encode(['Hotel pick-up and drop-off', 'Lunch at Heart Beach, Khorfakkan', 'Professional guide', 'Swimming with turtles', 'Oyster farm visit', 'Free beach access']),
                'sort_order' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Abu Dhabi Panoramic Bus + Ferrari World',
                'slug' => 'abu-dhabi-panoramic-bus-ferrari-world',
                'route_label' => 'Abu Dhabi route',
                'price' => 'AED 945 per person',
                'panel_price' => 'AED 945 / person',
                'category_label' => 'Grand Mosque and Ferrari World',
                'short_description' => 'A focused Abu Dhabi day including Sheikh Zayed Grand Mosque, Ferrari World Theme Park admission, lunch, guide, and refreshments.',
                'best_for' => 'Best for guests who want a focused Abu Dhabi day combining one major cultural landmark with Ferrari World Theme Park access.',
                'card_image_url' => 'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?auto=format&fit=crop&w=1300&q=85',
                'detail_image_url' => 'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?auto=format&fit=crop&w=1600&q=85',
                'tags' => json_encode(['Ferrari World', 'Mosque visit', 'Lunch']),
                'highlights' => json_encode(['Sheikh Zayed Grand Mosque', 'Ferrari World Theme Park']),
                'included' => json_encode(['Hotel pick-up and drop-off', 'Professional guide', 'Lunch', 'Water and soft drinks', 'Admission ticket to Ferrari World Theme Park']),
                'sort_order' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_tour_listings');
    }
};

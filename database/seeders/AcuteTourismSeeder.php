<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Experience;
use Illuminate\Database\Seeder;

class AcuteTourismSeeder extends Seeder
{
    public function run(): void
    {
        $collections = collect([
            [
                'name' => 'Luxury Desert Safaris',
                'slug' => 'luxury-desert-safaris',
                'summary' => 'Sunset drives, private camps, and heritage-led itineraries.',
                'description' => 'A premium collection of desert experiences built around privacy, hospitality, and stronger storytelling.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/03/29/desert-safari-premium-miniatura.png',
                'sort_order' => 1,
                'is_featured' => true,
            ],
            [
                'name' => 'Yacht Experiences',
                'slug' => 'yacht-experiences',
                'summary' => 'Marina charters, celebrations, and polished on-water hosting.',
                'description' => 'A focused set of premium yacht-led experiences designed for couples, families, and events.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/04/25/yacht-party-miniatura.png',
                'sort_order' => 2,
                'is_featured' => true,
            ],
            [
                'name' => 'Family Dubai',
                'slug' => 'family-dubai',
                'summary' => 'Comfort-first experiences suitable for mixed-age groups.',
                'description' => 'Family-friendly experiences with better logistics, pickup clarity, and premium support.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/05/03/family-dinner-miniatura.png',
                'sort_order' => 3,
                'is_featured' => true,
            ],
            [
                'name' => 'Private Tours',
                'slug' => 'private-tours',
                'summary' => 'Dedicated transport and custom pacing for private travelers.',
                'description' => 'Private city and destination experiences with cleaner service packaging and itinerary flexibility.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/04/22/whatsapp-image-2025-04-18-at-200746.jpeg',
                'sort_order' => 4,
                'is_featured' => true,
            ],
        ])->mapWithKeys(function (array $collection) {
            $model = Collection::updateOrCreate(
                ['slug' => $collection['slug']],
                $collection,
            );

            return [$model->slug => $model];
        });

        $experiences = collect([
            [
                'title' => 'Private Heritage Desert Safari',
                'slug' => 'private-heritage-desert-safari',
                'category' => 'Desert',
                'short_description' => 'High-comfort desert experience with private hosting and premium dining.',
                'hero_summary' => 'A flagship desert itinerary framed for premium private travelers.',
                'description' => 'Designed as a flagship premium desert product with clearer inclusions, private pacing, and stronger hospitality cues.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/03/29/desert-safari-premium-miniatura.png',
                'gallery_images' => [
                    'https://acutetourism.org/uploads/0000/7/2025/03/29/desert-safari-top-miniatura.png',
                    'https://acutetourism.org/uploads/0000/7/2025/03/29/desert-safari-premium-miniatura.png',
                ],
                'highlights' => ['Private hosting', 'Premium camp setup', 'Sunset desert timing'],
                'inclusions' => ['Pickup and drop-off', 'Hosted camp access', 'Dinner experience'],
                'exclusions' => ['Professional photography', 'Personal shopping'],
                'duration' => '7 hours',
                'location' => 'Dubai Desert Conservation area',
                'pickup_note' => 'Downtown and Marina pickup available',
                'price_from' => 950,
                'currency' => 'AED',
                'tag' => 'Signature',
                'sort_order' => 1,
                'seo_title' => 'Private Heritage Desert Safari Dubai | Acute Tourism',
                'seo_description' => 'Premium private desert safari in Dubai with elevated service, hosted camp access, and curated evening pacing.',
                'is_featured' => true,
                'is_private' => true,
                'collections' => ['luxury-desert-safaris', 'private-tours'],
            ],
            [
                'title' => 'Premium Evening Desert Camp',
                'slug' => 'premium-evening-desert-camp',
                'category' => 'Desert',
                'short_description' => 'Refined camp experience with sunset dune program and service-led setup.',
                'hero_summary' => 'An elevated evening desert format for premium leisure travelers.',
                'description' => 'A more scalable desert offering that still feels elevated compared with generic safari listings.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/03/29/desert-safari-top-miniatura.png',
                'highlights' => ['Sunset timing', 'Refined camp presentation', 'Clear itinerary'],
                'inclusions' => ['Transfers', 'Camp access', 'Evening program'],
                'exclusions' => ['Private setup upgrade'],
                'duration' => '6 hours',
                'location' => 'Dubai Desert',
                'pickup_note' => 'Shared and private transfer options available',
                'price_from' => 650,
                'currency' => 'AED',
                'tag' => 'Best Seller',
                'sort_order' => 2,
                'seo_title' => 'Premium Evening Desert Camp Dubai | Acute Tourism',
                'seo_description' => 'Book a refined evening desert camp in Dubai with upgraded presentation and stronger service clarity.',
                'is_featured' => true,
                'is_private' => false,
                'collections' => ['luxury-desert-safaris'],
            ],
            [
                'title' => 'Sunset Marina Yacht Charter',
                'slug' => 'sunset-marina-yacht-charter',
                'category' => 'Yacht',
                'short_description' => 'Private yacht charter for sunset cruising, hosting, and premium photography.',
                'hero_summary' => 'A high-intent Dubai Marina yacht product for private and celebration bookings.',
                'description' => 'A direct-response yacht product with stronger packaging, itinerary framing, and premium positioning.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/04/25/yacht-party-miniatura.png',
                'gallery_images' => [
                    'https://acutetourism.org/uploads/0000/7/2025/05/03/lotus-vip-miniatura-1.png',
                    'https://acutetourism.org/uploads/0000/7/2025/04/25/yacht-party-miniatura.png',
                ],
                'highlights' => ['Private yacht', 'Sunset route', 'Event-friendly pacing'],
                'inclusions' => ['Captain and crew', 'Soft drinks', 'Marina departure'],
                'exclusions' => ['Catering upgrades'],
                'duration' => '2 hours',
                'location' => 'Dubai Marina',
                'pickup_note' => 'Boarding from Dubai Marina',
                'price_from' => 1800,
                'currency' => 'AED',
                'tag' => 'Concierge',
                'sort_order' => 3,
                'seo_title' => 'Sunset Marina Yacht Charter Dubai | Acute Tourism',
                'seo_description' => 'Private yacht charter in Dubai Marina for sunset cruises, celebrations, and premium on-water hosting.',
                'is_featured' => true,
                'is_private' => true,
                'collections' => ['yacht-experiences', 'private-tours'],
            ],
            [
                'title' => 'Bluewaters and JBR Coastal Cruise',
                'slug' => 'bluewaters-jbr-coastal-cruise',
                'category' => 'Yacht',
                'short_description' => 'A polished water itinerary for couples, groups, and celebrations.',
                'hero_summary' => 'A longer coastal route positioned for premium social and leisure bookings.',
                'description' => 'Premium yacht route designed to support higher-value leisure and event inquiries.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/05/03/lotus-vip-miniatura-1.png',
                'highlights' => ['Bluewaters route', 'Private charter', 'Extended itinerary'],
                'inclusions' => ['Crew', 'Soft refreshments', 'Cruising route'],
                'exclusions' => ['Premium catering'],
                'duration' => '3 hours',
                'location' => 'Dubai Marina and JBR',
                'pickup_note' => 'Departure from Marina',
                'price_from' => 2250,
                'currency' => 'AED',
                'tag' => 'Private',
                'sort_order' => 4,
                'seo_title' => 'Bluewaters and JBR Coastal Cruise | Acute Tourism',
                'seo_description' => 'Premium Dubai coastal yacht cruise covering Bluewaters and JBR for private groups and events.',
                'is_featured' => false,
                'is_private' => true,
                'collections' => ['yacht-experiences'],
            ],
            [
                'title' => 'Dubai Landmarks Chauffeured Tour',
                'slug' => 'dubai-landmarks-chauffeured-tour',
                'category' => 'City',
                'short_description' => 'Private SUV touring with flexible pacing and premium city stops.',
                'hero_summary' => 'A cleaner private city-touring product with executive-level presentation.',
                'description' => 'A clean private touring product replacing generic city-tour cards with a more executive feel.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/04/22/whatsapp-image-2025-04-18-at-200746.jpeg',
                'highlights' => ['Private SUV', 'Flexible pacing', 'Premium city route'],
                'inclusions' => ['Driver guide', 'Hotel pickup', 'Custom stop flow'],
                'exclusions' => ['Entry tickets'],
                'duration' => '8 hours',
                'location' => 'Dubai',
                'pickup_note' => 'Hotel pickup within Dubai',
                'price_from' => 1250,
                'currency' => 'AED',
                'tag' => 'Private',
                'sort_order' => 5,
                'seo_title' => 'Dubai Landmarks Chauffeured Tour | Acute Tourism',
                'seo_description' => 'Private chauffeured Dubai city tour with flexible pacing, curated stops, and premium service.',
                'is_featured' => true,
                'is_private' => true,
                'collections' => ['private-tours'],
            ],
            [
                'title' => 'Atlantis Family Day With Transfers',
                'slug' => 'atlantis-family-day-with-transfers',
                'category' => 'Water & Family',
                'short_description' => 'Family-focused day package with easier logistics and transfer support.',
                'hero_summary' => 'A family-led attraction package built around comfort and smoother planning.',
                'description' => 'Built for the family collection with emphasis on comfort, planning, and operational clarity.',
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/19/2025/09/22/c323d42674bf59c2bcdd832a5f56cfca.jpg',
                'highlights' => ['Family planning', 'Transfer support', 'Comfort-first flow'],
                'inclusions' => ['Transfers', 'Experience access'],
                'exclusions' => ['Food and beverage'],
                'duration' => 'Full day',
                'location' => 'Atlantis The Palm',
                'pickup_note' => 'Optional hotel transfer',
                'price_from' => 520,
                'currency' => 'AED',
                'tag' => 'Family',
                'sort_order' => 6,
                'seo_title' => 'Atlantis Family Day With Transfers | Acute Tourism',
                'seo_description' => 'Family-friendly Atlantis experience with transfer support and easier logistics for Dubai visitors.',
                'is_featured' => false,
                'is_private' => false,
                'collections' => ['family-dubai'],
            ],
        ]);

        $experiences->each(function (array $experience) use ($collections): void {
            $linkedCollectionSlugs = $experience['collections'];
            unset($experience['collections']);

            $model = Experience::updateOrCreate(
                ['slug' => $experience['slug']],
                $experience,
            );

            $sync = collect($linkedCollectionSlugs)
                ->mapWithKeys(fn (string $slug, int $index) => [
                    $collections[$slug]->id => ['sort_order' => $index + 1],
                ])
                ->all();

            $model->collections()->sync($sync);
        });
    }
}

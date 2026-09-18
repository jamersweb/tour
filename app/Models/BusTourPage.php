<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\UploadPath;
use Illuminate\Database\Eloquent\Model;

class BusTourPage extends Model
{
    protected $fillable = [
        'seo_title',
        'seo_description',
        'hero_eyebrow',
        'hero_title',
        'hero_description',
        'hero_primary_cta_label',
        'hero_secondary_cta_label',
        'hero_fomo_line',
        'hero_facts',
        'intro_mark',
        'intro_eyebrow',
        'intro_title',
        'intro_copy',
        'choice_media_label',
        'choice_media_title',
        'choice_media_copy',
        'choice_media_image_path',
        'choice_media_image_url',
        'choice_media_video_path',
        'choice_media_video_url',
        'choice_lines',
        'choice_primary_cta_label',
        'choice_secondary_cta_label',
        'routes_eyebrow',
        'routes_title',
        'routes_copy',
        'routes',
        'availability_note',
        'details_eyebrow',
        'details_title',
        'details_copy',
        'before_booking_eyebrow',
        'before_booking_title',
        'before_booking_copy',
        'before_booking_cards',
        'private_eyebrow',
        'private_title',
        'private_copy',
        'private_lines',
        'private_cta_label',
        'private_image_url',
        'private_image_path',
        'audience_eyebrow',
        'audience_title',
        'audience_copy',
        'audiences',
        'gallery_eyebrow',
        'gallery_title',
        'gallery_copy',
        'gallery_items',
        'gallery_note',
        'enquiry_eyebrow',
        'enquiry_title',
        'enquiry_copy',
        'enquiry_submit_label',
        'enquiry_processing_label',
        'enquiry_whatsapp_label',
        'enquiry_note',
        'whatsapp_url',
        'faq_eyebrow',
        'faq_title',
        'faqs',
        'sticky_label',
        'sticky_text',
        'sticky_cta_label',
    ];

    protected function casts(): array
    {
        return [
            'hero_facts' => 'array',
            'choice_lines' => 'array',
            'routes' => 'array',
            'before_booking_cards' => 'array',
            'private_lines' => 'array',
            'audiences' => 'array',
            'gallery_items' => 'array',
            'faqs' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], static::defaults());
    }

    public function getPrivateImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setPrivateImagePathAttribute(mixed $value): void
    {
        $this->attributes['private_image_path'] = UploadPath::normalize($value);
    }

    public function getPrivateImageResolvedUrlAttribute(): ?string
    {
        if ($this->private_image_path) {
            return MediaUrl::upload($this->private_image_path);
        }

        return MediaUrl::normalize($this->private_image_url);
    }

    public function getChoiceMediaImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setChoiceMediaImagePathAttribute(mixed $value): void
    {
        $this->attributes['choice_media_image_path'] = UploadPath::normalize($value);
    }

    public function getChoiceMediaVideoPathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setChoiceMediaVideoPathAttribute(mixed $value): void
    {
        $this->attributes['choice_media_video_path'] = UploadPath::normalize($value);
    }

    public function getChoiceMediaImageResolvedUrlAttribute(): ?string
    {
        if ($this->choice_media_image_path) {
            return MediaUrl::upload($this->choice_media_image_path);
        }

        return MediaUrl::normalize($this->choice_media_image_url);
    }

    public function getChoiceMediaVideoResolvedUrlAttribute(): ?string
    {
        if ($this->choice_media_video_path) {
            return MediaUrl::upload($this->choice_media_video_path);
        }

        return MediaUrl::normalize($this->choice_media_video_url);
    }

    public function setGalleryItemsAttribute(mixed $value): void
    {
        if (! is_array($value)) {
            $this->attributes['gallery_items'] = $value === null ? null : json_encode($value);

            return;
        }

        $this->attributes['gallery_items'] = json_encode(collect($value)
            ->map(function (mixed $item): array {
                $item = is_array($item) ? $item : [];
                $item['image_uploads'] = UploadPath::normalizeArray($item['image_uploads'] ?? [], preserveExternal: false);
                $item['video_uploads'] = UploadPath::normalizeArray($item['video_uploads'] ?? [], preserveExternal: false);

                return $item;
            })
            ->values()
            ->all());
    }

    public function publicPayload(): array
    {
        return [
            'hero' => [
                'eyebrow' => $this->hero_eyebrow,
                'title' => $this->hero_title,
                'description' => $this->hero_description,
                'primaryCtaLabel' => $this->hero_primary_cta_label,
                'secondaryCtaLabel' => $this->hero_secondary_cta_label,
                'fomoLine' => $this->hero_fomo_line,
                'facts' => $this->hero_facts ?? [],
            ],
            'intro' => [
                'mark' => $this->intro_mark,
                'eyebrow' => $this->intro_eyebrow,
                'title' => $this->intro_title,
                'copy' => $this->intro_copy,
            ],
            'choice' => [
                'mediaLabel' => $this->choice_media_label,
                'mediaTitle' => $this->choice_media_title,
                'mediaCopy' => $this->choice_media_copy,
                'mediaImageUrl' => $this->choice_media_image_resolved_url,
                'mediaVideoUrl' => $this->choice_media_video_resolved_url,
                'lines' => $this->choice_lines ?? [],
                'primaryCtaLabel' => $this->choice_primary_cta_label,
                'secondaryCtaLabel' => $this->choice_secondary_cta_label,
            ],
            'routesSection' => [
                'eyebrow' => $this->routes_eyebrow,
                'title' => $this->routes_title,
                'copy' => $this->routes_copy,
                'items' => $this->routes ?? [],
                'availabilityNote' => $this->availability_note,
            ],
            'details' => [
                'eyebrow' => $this->details_eyebrow,
                'title' => $this->details_title,
                'copy' => $this->details_copy,
            ],
            'beforeBooking' => [
                'eyebrow' => $this->before_booking_eyebrow,
                'title' => $this->before_booking_title,
                'copy' => $this->before_booking_copy,
                'cards' => $this->before_booking_cards ?? [],
            ],
            'privateSection' => [
                'eyebrow' => $this->private_eyebrow,
                'title' => $this->private_title,
                'copy' => $this->private_copy,
                'lines' => $this->private_lines ?? [],
                'ctaLabel' => $this->private_cta_label,
                'imageUrl' => $this->private_image_resolved_url,
            ],
            'audience' => [
                'eyebrow' => $this->audience_eyebrow,
                'title' => $this->audience_title,
                'copy' => $this->audience_copy,
                'items' => $this->audiences ?? [],
            ],
            'gallery' => [
                'eyebrow' => $this->gallery_eyebrow,
                'title' => $this->gallery_title,
                'copy' => $this->gallery_copy,
                'items' => $this->publicGalleryItems(),
                'note' => $this->gallery_note,
            ],
            'enquiry' => [
                'eyebrow' => $this->enquiry_eyebrow,
                'title' => $this->enquiry_title,
                'copy' => $this->enquiry_copy,
                'submitLabel' => $this->enquiry_submit_label,
                'processingLabel' => $this->enquiry_processing_label,
                'whatsappLabel' => $this->enquiry_whatsapp_label,
                'note' => $this->enquiry_note,
                'whatsappUrl' => $this->whatsapp_url,
            ],
            'faq' => [
                'eyebrow' => $this->faq_eyebrow,
                'title' => $this->faq_title,
                'items' => $this->faqs ?? [],
            ],
            'sticky' => [
                'label' => $this->sticky_label,
                'text' => $this->sticky_text,
                'ctaLabel' => $this->sticky_cta_label,
            ],
        ];
    }

    protected function publicGalleryItems(): array
    {
        return collect($this->gallery_items ?? [])
            ->map(function (mixed $item): array {
                $item = is_array($item) ? $item : [];
                $uploadedImages = collect($item['image_uploads'] ?? [])
                    ->map(fn ($path) => MediaUrl::upload(UploadPath::normalize($path, preserveExternal: false)))
                    ->filter()
                    ->values()
                    ->all();

                $uploadedVideos = collect($item['video_uploads'] ?? [])
                    ->map(fn ($path) => MediaUrl::upload(UploadPath::normalize($path, preserveExternal: false)))
                    ->filter()
                    ->values()
                    ->all();

                $urlImages = collect($item['images'] ?? [])
                    ->map(fn ($url) => MediaUrl::normalize($url))
                    ->filter()
                    ->values()
                    ->all();

                $urlVideos = collect($item['videos'] ?? [])
                    ->map(fn ($url) => MediaUrl::normalize($url))
                    ->filter()
                    ->values()
                    ->all();

                $item['images'] = array_values(array_unique([...$uploadedImages, ...$urlImages]));
                $item['videos'] = array_values(array_unique([...$uploadedVideos, ...$urlVideos]));
                unset($item['image_uploads'], $item['video_uploads']);

                return $item;
            })
            ->values()
            ->all();
    }

    public static function defaults(): array
    {
        return [
            'seo_title' => 'Luxury Bus Tour Dubai | Panoramic UAE Bus Trips',
            'seo_description' => "Join Acute Tourism's luxury bus tour Dubai experience with panoramic views, exclusive seats, curated UAE day trips, hotel pick-up, sightseeing, meals, and guided support.",
            'hero_eyebrow' => 'Panoramic Bus Dubai',
            'hero_title' => 'Luxury Bus Tour Dubai',
            'hero_description' => "Travel through Dubai, Al Ain, Fujairah or Abu Dhabi in a more comfortable and curated way. Acute Tourism's luxury bus tour Dubai experience is designed for premium guests who prefer hotel pick-up, guided sightseeing, included meals or tastings, selected attractions and a smoother day out without the feel of a crowded group tour.",
            'hero_primary_cta_label' => 'View Packages',
            'hero_secondary_cta_label' => 'Request Availability',
            'hero_fomo_line' => 'Limited seats per scheduled tour - Early enquiry recommended',
            'hero_facts' => [
                ['value' => '12 Guests Only', 'label' => 'Intentionally limited per departure'],
                ['value' => '18 Seats', 'label' => 'More room on board'],
                ['value' => 'Hotel Pick-up', 'label' => 'Included with every route'],
                ['value' => 'High-demand Dates', 'label' => 'Availability confirmed on request'],
            ],
            'intro_mark' => "Come\non\nboard",
            'intro_eyebrow' => 'Why guests choose it',
            'intro_title' => 'A hosted UAE day experience with comfort, access and a premium pace',
            'intro_copy' => 'The experience is built for guests who want the day arranged properly: hotel pick-up, a comfortable panoramic bus, a professional guide, selected route highlights, lunch or tasting value, refreshments and return drop-off.',
            'choice_media_label' => 'Luxury panoramic bus tour media area',
            'choice_media_title' => 'Watch the experience',
            'choice_media_copy' => 'Experience video area: bus interior, guest welcome, route moments and destination highlights.',
            'choice_lines' => [
                ['number' => '01', 'title' => 'Limited scheduled capacity', 'copy' => 'Each scheduled tour is intentionally limited to 12 guests, creating a more exclusive, spacious and personal experience.'],
                ['number' => '02', 'title' => 'Hotel pick-up and return', 'copy' => 'Start and end from your hotel, making the tour easier for tourists, families and visiting guests.'],
                ['number' => '03', 'title' => 'Professional guided route', 'copy' => 'Enjoy guided sightseeing, landmark context and assistance throughout the journey.'],
                ['number' => '04', 'title' => 'Lunch, tastings or attraction value', 'copy' => 'Each tour includes food, drinks and route-specific value such as Al Ain Zoo or Ferrari World where applicable.'],
                ['number' => '05', 'title' => 'Private group option', 'copy' => 'Reserve the bus for family outings, birthdays, corporate groups, travel agencies or special occasions.'],
            ],
            'choice_primary_cta_label' => 'Compare Tours',
            'choice_secondary_cta_label' => 'Private Group Enquiry',
            'routes_eyebrow' => 'Packages and prices',
            'routes_title' => 'Choose your panoramic bus tour',
            'routes_copy' => 'Four curated routes from Dubai, each with hotel pick-up and drop-off, a professional guide, lunch or tasting value, water and soft drinks.',
            'routes' => static::defaultRoutes(),
            'availability_note' => 'Scheduled seats are limited and preferred dates may close once capacity is reached. For families, celebrations, corporate groups or travel agencies, private bus requests are recommended for better date control.',
            'details_eyebrow' => 'Tour details',
            'details_title' => 'What each tour includes',
            'details_copy' => 'Each route is structured around a clear experience theme, arranged transport, guided sightseeing, and included food or attraction value.',
            'before_booking_eyebrow' => 'Before you book',
            'before_booking_title' => 'Clear answers for confident booking',
            'before_booking_copy' => 'Essential details to help you understand what is included, what may depend on availability and how your booking is confirmed.',
            'before_booking_cards' => [
                ['title' => 'Pick-up and timing', 'copy' => 'Hotel pick-up and drop-off are included. Exact pick-up timing, route order and return timing are confirmed after availability is checked.'],
                ['title' => 'Fujairah marine activities', 'copy' => 'Swimming with turtles, shark sessions, beach access and oyster farm visits are confirmed subject to supplier availability, weather, sea conditions and guest safety requirements.'],
                ['title' => 'Attraction access', 'copy' => 'Al Ain Zoo and Ferrari World admission are included in their respective tours. Attraction operating hours, ride availability and entry rules may apply.'],
            ],
            'private_eyebrow' => 'Private bus enquiry',
            'private_title' => 'Reserve the bus for your own group',
            'private_copy' => 'The panoramic bus can also be requested for private groups, families, friends, corporate teams, celebrations, school groups, travel agencies and custom UAE experiences.',
            'private_lines' => [
                ['label' => 'Best for', 'value' => 'Families, companies, groups and occasions'],
                ['label' => 'Route options', 'value' => 'Dubai, Al Ain, Fujairah, Abu Dhabi or custom'],
                ['label' => 'Add-ons', 'value' => 'Shopping, photography and videography'],
            ],
            'private_cta_label' => 'Request Private Bus',
            'audience_eyebrow' => 'Best for',
            'audience_title' => 'Designed for guests who value ease and exclusivity',
            'audience_copy' => 'The panoramic bus format is especially useful when you want a premium day out arranged for you.',
            'audiences' => [
                ['title' => 'Premium travellers', 'copy' => 'For tourists who want a comfortable UAE day tour with hotel pick-up, guide support and route planning already handled.'],
                ['title' => 'Residents hosting guests', 'copy' => 'For UAE residents who want to impress family or friends with a polished, easy-to-book experience.'],
                ['title' => 'Families and small groups', 'copy' => 'For guests who prefer a more comfortable alternative to arranging cars, tickets, lunch and timings separately.'],
                ['title' => 'Private occasions', 'copy' => 'For birthdays, corporate outings, school groups, social clubs and travel agencies that want a ready-made group journey.'],
            ],
            'gallery_eyebrow' => 'Experience media',
            'gallery_title' => 'A closer look at the journey',
            'gallery_copy' => 'See the bus, onboard comfort, hosted moments and route highlights before you request availability.',
            'gallery_items' => static::defaultGalleryItems(),
            'gallery_note' => 'Browse the bus setup, seating, destination highlights and onboard hospitality before requesting availability.',
            'enquiry_eyebrow' => 'Check availability',
            'enquiry_title' => 'Request your panoramic bus experience',
            'enquiry_copy' => 'Share your preferred route, date and group details. The Acute Tourism team will confirm availability, hotel pick-up timing, route flow, attraction access and private bus options before payment.',
            'enquiry_submit_label' => 'Send Enquiry',
            'enquiry_processing_label' => 'Sending...',
            'enquiry_whatsapp_label' => 'Prefer WhatsApp? Speak to our team directly',
            'enquiry_note' => 'Submitting this form does not confirm booking. The team will confirm seat availability, hotel pick-up details, final timing and any supplier-dependent activities before payment.',
            'whatsapp_url' => 'https://wa.me/971521926984?text=Hello%20Acute%20Tourism%2C%20I%20want%20to%20check%20availability%20for%20the%20panoramic%20bus%20tour.',
            'faq_eyebrow' => 'Questions guests may ask',
            'faq_title' => 'Frequently asked questions',
            'faqs' => [
                ['question' => 'Are hotel pick-up and drop-off included?', 'answer' => 'Yes. Hotel pick-up and drop-off are included across the four panoramic bus tour options. Exact pick-up time and coverage will be confirmed by the Acute Tourism team after your enquiry.'],
                ['question' => 'Are lunch and drinks included?', 'answer' => 'Yes. Lunch, water and soft drinks are included across the routes. The Dubai route also includes food tasting and a sundowner at The Palm.'],
                ['question' => 'Can the bus be booked privately?', 'answer' => 'Yes. The panoramic bus can be requested for families, friends, corporate groups, school groups, travel agencies and special occasions. Private requests are recommended when you need more control over date, route and group arrangements.'],
                ['question' => 'Are marine activities on the Fujairah tour guaranteed?', 'answer' => 'Marine activities such as turtle swimming, shark sessions, beach access and oyster farm visits are reconfirmed before booking because they may depend on supplier operations, weather, sea conditions and safety requirements.'],
                ['question' => 'What should guests bring?', 'answer' => 'Comfortable clothing is recommended. For mosque visits, modest clothing is required. For Fujairah, guests may bring swimwear, a towel, sunscreen and sunglasses. A valid ID may be required for selected attractions.'],
                ['question' => 'Is photography or videography available?', 'answer' => 'Yes. Professional photography and videography are optional add-ons and can be requested during enquiry.'],
                ['question' => 'How do I confirm my booking?', 'answer' => 'Submit the enquiry form or contact the team on WhatsApp. Acute Tourism will confirm availability, hotel pick-up timing, final route flow, attraction access and payment details before your booking is confirmed.'],
                ['question' => 'How do I choose the right route?', 'answer' => 'Choose Dubai for food and city highlights, Al Ain for family and wildlife, Fujairah for coastal and marine experiences, and Abu Dhabi for Grand Mosque and Ferrari World.'],
            ],
            'sticky_label' => 'Limited seats',
            'sticky_text' => 'on scheduled departures',
            'sticky_cta_label' => 'Request Availability',
        ];
    }

    protected static function defaultRoutes(): array
    {
        return [
            [
                'key' => 'dubai',
                'title' => 'Dubai Panoramic Bus + Food Tasting',
                'day' => 'Dubai route',
                'price' => 'AED 499 per person',
                'panelPrice' => 'AED 499 / person',
                'label' => 'City, food and sundowner',
                'copy' => 'A city-focused luxury bus tour with hotel pick-up, Museum of the Future photo stop, Al Seef, DIFC, food tasting, lunch, and a sundowner at The Palm.',
                'bestFor' => 'Best for visitors, residents hosting guests, couples and small groups who want a relaxed city route with selected Dubai highlights and food-focused moments.',
                'tags' => ['Food tasting', 'Lunch', 'Guide'],
                'highlights' => ['Hotel pick-up from selected Dubai areas', 'Museum of the Future photo stop', 'Al Seef photo stop', 'DIFC photo stop', 'Sundowner at The Palm'],
                'included' => ['Hotel pick-up and drop-off', 'Professional guide', 'Food tasting', 'Lunch', 'Water and soft drinks'],
            ],
            [
                'key' => 'alain',
                'title' => 'Al Ain Panoramic Bus + Al Ain Zoo',
                'day' => 'Al Ain route',
                'price' => 'AED 499 per person',
                'panelPrice' => 'AED 499 / person',
                'label' => 'Wildlife and heritage',
                'copy' => 'A family-friendly journey with Al Ain Zoo admission, Jebel Hafeet, Al Jahili Fort, Hili Archaeological Park, lunch, and guide.',
                'bestFor' => 'Best for families, residents and guests who want a comfortable wildlife and heritage day outside Dubai with attraction access already included.',
                'tags' => ['Zoo ticket', 'Family-friendly', 'Lunch'],
                'highlights' => ['National Museum photo stop', 'Hili Archaeological Park photo stop', 'Jebel Hafeet', 'Al Jahili Fort', 'Al Ain Zoo visit'],
                'included' => ['Hotel pick-up and drop-off', 'Al Ain Zoo admission ticket', 'Professional guide', 'Lunch', 'Water and soft drinks'],
            ],
            [
                'key' => 'fujairah',
                'title' => 'Fujairah Panoramic Bus',
                'day' => 'Fujairah route',
                'price' => 'AED 699 per person',
                'panelPrice' => 'AED 699 / person',
                'label' => 'Coastal and marine experience',
                'copy' => 'A coastal route with Friday Market, Al Hayl Castle, Khorfakkan Waterfall, oyster farm visit, beach access, and marine activities.',
                'bestFor' => 'Best for guests looking for the most distinctive route: a coastal journey with heritage stops, Khorfakkan scenery, lunch by the beach and marine-focused experiences.',
                'tags' => ['Oyster farm', 'Beach access', 'Marine experience'],
                'highlights' => ['Hotel pick-up from selected Dubai areas', 'Friday Market shopping and photo stop', 'Al Hayl Castle', 'Khorfakkan Waterfall', 'Return transfer to your hotel'],
                'included' => ['Hotel pick-up and drop-off', 'Lunch at Heart Beach, Khorfakkan', 'Professional guide', 'Swimming with turtles', 'Oyster farm visit', 'Free beach access'],
            ],
            [
                'key' => 'abudhabi',
                'title' => 'Abu Dhabi Panoramic Bus + Ferrari World',
                'day' => 'Abu Dhabi route',
                'price' => 'AED 945 per person',
                'panelPrice' => 'AED 945 / person',
                'label' => 'Grand Mosque and Ferrari World',
                'copy' => 'A focused Abu Dhabi day including Sheikh Zayed Grand Mosque, Ferrari World Theme Park admission, lunch, guide, and refreshments.',
                'bestFor' => 'Best for guests who want a focused Abu Dhabi day combining one major cultural landmark with Ferrari World Theme Park access.',
                'tags' => ['Ferrari World', 'Mosque visit', 'Lunch'],
                'highlights' => ['Sheikh Zayed Grand Mosque', 'Ferrari World Theme Park'],
                'included' => ['Hotel pick-up and drop-off', 'Professional guide', 'Lunch', 'Water and soft drinks', 'Admission ticket to Ferrari World Theme Park'],
            ],
        ];
    }

    protected static function defaultGalleryItems(): array
    {
        return [
            ['title' => 'Luxury panoramic bus', 'copy' => 'Exterior and route visuals from the experience.', 'images' => ['https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Bus interior', 'copy' => 'Seating, comfort, space and onboard setup.', 'images' => ['https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Dubai city route', 'copy' => 'City views, photo stops and sundowner atmosphere.', 'images' => ['https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Fujairah coastal route', 'copy' => 'Coastal, beach and marine moments.', 'images' => ['https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Al Ain family route', 'copy' => 'Wildlife, heritage and mountain scenery.', 'images' => ['https://images.unsplash.com/photo-1551969014-7d2c4cddf0b6?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Abu Dhabi route', 'copy' => 'Grand Mosque and Ferrari World highlights.', 'images' => ['https://images.unsplash.com/photo-1512632578888-169bbbc64f33?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Lunch and tastings', 'copy' => 'Food tasting, lunch and refreshment moments.', 'images' => ['https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1543353071-873f17a7a088?auto=format&fit=crop&w=1300&q=85']],
            ['title' => 'Private groups', 'copy' => 'Family, corporate and group travel moments.', 'images' => ['https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=1300&q=85', 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1300&q=85']],
        ];
    }
}

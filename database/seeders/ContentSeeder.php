<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Faq;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            [
                'title' => 'How to Choose a Premium Desert Safari in Dubai',
                'slug' => 'choose-premium-desert-safari-dubai',
                'category' => 'Desert Guide',
                'excerpt' => 'What premium travelers actually look for when comparing desert safari operators in Dubai.',
                'content' => "A premium desert safari should be judged on operations, not just visuals.\n\nLook first at pickup clarity, hosting quality, camp environment, vehicle standards, and how private the pacing really is. Generic suppliers tend to sell similar imagery with inconsistent delivery.\n\nA stronger operator explains what is included, what remains optional, and how the evening is sequenced. That transparency is one of the clearest trust signals for higher-value travelers.",
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/03/29/desert-safari-premium-miniatura.png',
                'read_time' => 6,
                'sort_order' => 1,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'seo_title' => 'How to Choose a Premium Desert Safari in Dubai | Acute Tourism',
                'seo_description' => 'A practical guide to evaluating premium desert safari experiences in Dubai before booking.',
            ],
            [
                'title' => 'Private Yacht Charter Dubai: What Matters Before You Book',
                'slug' => 'private-yacht-charter-dubai-guide',
                'category' => 'Yacht Guide',
                'excerpt' => 'A concise buyer guide for travelers comparing private yacht experiences in Dubai.',
                'content' => "Yacht buyers compare route quality, boarding logistics, crew standards, and how clearly upgrades are presented.\n\nFor premium positioning, packaging matters as much as inventory. Clear charter duration, inclusions, marina details, and celebration add-ons reduce friction and support higher-intent inquiries.",
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/04/25/yacht-party-miniatura.png',
                'read_time' => 5,
                'sort_order' => 2,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now()->subDays(4),
                'seo_title' => 'Private Yacht Charter Dubai Guide | Acute Tourism',
                'seo_description' => 'A buyer guide covering the main decision points for private yacht charters in Dubai.',
            ],
            [
                'title' => 'Family-Friendly Dubai Experiences Without Planning Friction',
                'slug' => 'family-friendly-dubai-experiences',
                'category' => 'Family Travel',
                'excerpt' => 'Why comfort, timing, and pickup clarity matter more than sheer activity count for families.',
                'content' => "Family products convert better when logistics are explicit.\n\nParents respond to transfer clarity, realistic timing, age suitability, and straightforward inclusion lists. A curated family collection should reduce planning work, not increase it.",
                'hero_image_path' => 'https://acutetourism.org/uploads/0000/7/2025/05/03/family-dinner-miniatura.png',
                'read_time' => 4,
                'sort_order' => 3,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'seo_title' => 'Family-Friendly Dubai Experiences | Acute Tourism',
                'seo_description' => 'A guide to choosing family-focused Dubai experiences with simpler logistics and better comfort.',
            ],
        ])->each(fn (array $article) => Article::updateOrCreate(['slug' => $article['slug']], $article));

        collect([
            [
                'question' => 'Is online payment live yet?',
                'answer' => 'Not yet. The rebuild is currently prioritizing premium content, inquiry flow, and CMS structure before production checkout goes live.',
                'category' => 'Booking',
                'sort_order' => 1,
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'question' => 'Which gateway will be used for checkout?',
                'answer' => 'The production checkout will use Network Payment Gateway.',
                'category' => 'Payments',
                'sort_order' => 2,
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'question' => 'Can we request private or custom itineraries?',
                'answer' => 'Yes. The contact and inquiry flow is being built around concierge-led planning for private tours, yacht charters, and custom group requests.',
                'category' => 'Planning',
                'sort_order' => 3,
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'question' => 'Will this new build replace the current site?',
                'answer' => 'Yes. This Laravel, Vue, and Inertia application is the foundation for the full replacement of the current production site.',
                'category' => 'Rebuild',
                'sort_order' => 4,
                'is_featured' => false,
                'is_published' => true,
            ],
        ])->each(function (array $faq): void {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        });
    }
}

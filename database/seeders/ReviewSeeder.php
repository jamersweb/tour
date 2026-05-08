<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Package;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $experienceReviews = [
            'private-heritage-desert-safari' => [
                [
                    'author_name' => 'Hannah R.',
                    'rating' => 5,
                    'title' => 'Smooth from pickup to dinner',
                    'quote' => 'The pace felt private, the hosting was polished, and the evening never felt rushed.',
                    'tag' => 'Couples booking',
                    'source' => 'Acute Tourism',
                    'sort_order' => 1,
                    'is_featured' => true,
                    'is_published' => true,
                    'reviewed_at' => now()->subWeeks(3),
                ],
                [
                    'author_name' => 'Michael T.',
                    'rating' => 5,
                    'title' => 'Better than a standard safari',
                    'quote' => 'We booked this because the inclusions were clearer, and the real experience matched that promise.',
                    'tag' => 'Private traveler',
                    'source' => 'Acute Tourism',
                    'sort_order' => 2,
                    'is_featured' => true,
                    'is_published' => true,
                    'reviewed_at' => now()->subWeeks(5),
                ],
                [
                    'author_name' => 'Sara K.',
                    'rating' => 4,
                    'title' => 'Well hosted and comfortable',
                    'quote' => 'Pickup was on time, the camp setup felt premium, and the team handled the evening well.',
                    'tag' => 'Family booking',
                    'source' => 'Acute Tourism',
                    'sort_order' => 3,
                    'is_featured' => false,
                    'is_published' => true,
                    'reviewed_at' => now()->subWeeks(7),
                ],
            ],
            'premium-evening-desert-camp' => [
                [
                    'author_name' => 'Lewis B.',
                    'rating' => 5,
                    'title' => 'Clear itinerary and strong service',
                    'quote' => 'Everything ran to schedule and the setup looked much more refined than most desert camp tours.',
                    'tag' => 'Leisure traveler',
                    'source' => 'Acute Tourism',
                    'sort_order' => 1,
                    'is_featured' => true,
                    'is_published' => true,
                    'reviewed_at' => now()->subWeeks(2),
                ],
                [
                    'author_name' => 'Nadia P.',
                    'rating' => 4,
                    'title' => 'Good value for the format',
                    'quote' => 'The evening felt organized and comfortable, with enough structure to keep the experience easy.',
                    'tag' => 'Friends trip',
                    'source' => 'Acute Tourism',
                    'sort_order' => 2,
                    'is_featured' => false,
                    'is_published' => true,
                    'reviewed_at' => now()->subWeeks(6),
                ],
            ],
        ];

        foreach ($experienceReviews as $slug => $reviews) {
            $experience = Experience::query()->where('slug', $slug)->first();

            if (! $experience) {
                continue;
            }

            $this->syncReviews($experience, $reviews);
        }

        $packageReviews = [
            'dazzling-dubai-escape-8d7n' => [
                [
                    'author_name' => 'Emma J.',
                    'rating' => 5,
                    'title' => 'Easy planning for a multi-day trip',
                    'quote' => 'The package saved us time because the structure was already there and the inclusions were easy to understand.',
                    'tag' => 'Holiday package',
                    'source' => 'Acute Tourism',
                    'sort_order' => 1,
                    'is_featured' => true,
                    'is_published' => true,
                    'reviewed_at' => now()->subMonth(),
                ],
                [
                    'author_name' => 'Farhan A.',
                    'rating' => 4,
                    'title' => 'Strong combination of comfort and coverage',
                    'quote' => 'Good pacing, reliable coordination, and a better overall structure than piecing the trip together ourselves.',
                    'tag' => 'Family package',
                    'source' => 'Acute Tourism',
                    'sort_order' => 2,
                    'is_featured' => false,
                    'is_published' => true,
                    'reviewed_at' => now()->subWeeks(8),
                ],
            ],
        ];

        foreach ($packageReviews as $slug => $reviews) {
            $package = Package::query()->where('slug', $slug)->first();

            if (! $package) {
                continue;
            }

            $this->syncReviews($package, $reviews);
        }

        $tourReviews = [
            'dummy-heritage-tour' => [
                [
                    'author_name' => 'Aisha M.',
                    'rating' => 5,
                    'title' => 'Helpful for testing the new tour layout',
                    'quote' => 'This dummy entry is seeded with real review fields so the admin and public page can be validated properly.',
                    'tag' => 'Seeded example',
                    'source' => 'Acute Tourism',
                    'sort_order' => 1,
                    'is_featured' => true,
                    'is_published' => true,
                    'reviewed_at' => now()->subDays(10),
                ],
            ],
        ];

        foreach ($tourReviews as $slug => $reviews) {
            $tour = Tour::query()->where('slug', $slug)->first();

            if (! $tour) {
                continue;
            }

            $this->syncReviews($tour, $reviews);
        }
    }

    protected function syncReviews($model, array $reviews): void
    {
        foreach ($reviews as $review) {
            Review::query()->updateOrCreate(
                [
                    'reviewable_type' => $model::class,
                    'reviewable_id' => $model->getKey(),
                    'author_name' => $review['author_name'],
                    'quote' => $review['quote'],
                ],
                $review + [
                    'reviewable_type' => $model::class,
                    'reviewable_id' => $model->getKey(),
                ],
            );
        }
    }
}

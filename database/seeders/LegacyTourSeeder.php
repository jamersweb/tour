<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Experience;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

class LegacyTourSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/current-tours.json');

        if (! file_exists($path)) {
            return;
        }

        /** @var array<int, array<string, mixed>> $items */
        $items = json_decode(file_get_contents($path), true) ?? [];

        $collections = Collection::query()
            ->get()
            ->keyBy('slug');

        collect($items)
            ->each(function (array $tour, int $index) use ($collections): void {
                $category = $this->resolveCategory($tour);
                $collectionSlugs = $this->resolveCollections($tour, $category);
                $description = $this->cleanText($tour['description'] ?? '') ?: $this->cleanText($tour['meta'] ?? '');
                $shortDescription = Str::limit($this->cleanText($tour['meta'] ?? $description), 220, '');
                $heroSummary = $this->firstSentence($description) ?: $shortDescription;
                $heroImage = $tour['hero'] ?: Arr::first($tour['gallery']);
                $gallery = collect($tour['gallery'] ?? [])
                    ->map(fn ($item) => $this->cleanText((string) $item))
                    ->filter()
                    ->reject(fn (string $item) => $item === $heroImage)
                    ->take(4)
                    ->values()
                    ->all();
                $inclusions = collect($tour['includes'] ?? [])
                    ->map(fn ($item) => $this->cleanText((string) $item))
                    ->filter()
                    ->take(5)
                    ->values()
                    ->all();

                $experience = Experience::query()->updateOrCreate(
                    ['slug' => $tour['slug']],
                    [
                        'title' => $this->cleanText($tour['title'] ?? Str::headline(str_replace('-', ' ', $tour['slug']))),
                        'category' => $category,
                        'short_description' => $shortDescription ?: Str::limit($description, 220, ''),
                        'hero_summary' => $heroSummary,
                        'description' => $description,
                        'hero_image_path' => $heroImage,
                        'gallery_images' => $gallery,
                        'highlights' => $this->resolveHighlights($tour, $category),
                        'inclusions' => $inclusions,
                        'exclusions' => ['Personal expenses', 'Optional upgrades not selected'],
                        'duration' => $this->cleanText($tour['duration'] ?? '') ?: null,
                        'location' => $this->normalizeLocation($tour['location'] ?? ''),
                        'pickup_note' => $this->pickupNoteForCategory($category),
                        'price_from' => null,
                        'currency' => 'AED',
                        'tag' => $this->resolveTag($tour, $category),
                        'sort_order' => 50 + $index,
                        'seo_title' => $this->cleanText(($tour['title'] ?? '').' | Acute Tourism'),
                        'seo_description' => Str::limit($shortDescription ?: $description, 320, ''),
                        'is_featured' => $index < 6,
                        'is_private' => $this->isPrivate($tour),
                        'is_active' => true,
                    ],
                );

                $sync = collect($collectionSlugs)
                    ->filter(fn (string $slug) => $collections->has($slug))
                    ->mapWithKeys(fn (string $slug, int $position) => [
                        $collections[$slug]->id => ['sort_order' => $position + 1],
                    ])
                    ->all();

                if ($sync !== []) {
                    $experience->collections()->syncWithoutDetaching($sync);
                }
            });
    }

    protected function resolveCategory(array $tour): string
    {
        $haystack = Str::lower(($tour['title'] ?? '').' '.($tour['slug'] ?? '').' '.($tour['location'] ?? ''));

        if (Str::contains($haystack, ['desert', 'balloon', 'buggy', 'quad'])) {
            return 'Desert';
        }

        if (Str::contains($haystack, ['yacht', 'cruise', 'marina'])) {
            return 'Yacht';
        }

        if (Str::contains($haystack, ['aquaventure', 'wild wadi', 'ski dubai', 'img', 'ferrari world'])) {
            return 'Water & Family';
        }

        return 'City';
    }

    /**
     * @return array<int, string>
     */
    protected function resolveCollections(array $tour, string $category): array
    {
        $collections = match ($category) {
            'Desert' => ['luxury-desert-safaris'],
            'Yacht' => ['yacht-experiences'],
            'Water & Family' => ['family-dubai'],
            default => ['private-tours'],
        };

        $haystack = Str::lower(($tour['title'] ?? '').' '.($tour['slug'] ?? ''));

        if (Str::contains($haystack, ['private', 'transfer', 'helicopter', 'city tour'])) {
            $collections[] = 'private-tours';
        }

        return array_values(array_unique($collections));
    }

    /**
     * @return array<int, string>
     */
    protected function resolveHighlights(array $tour, string $category): array
    {
        $highlights = new SupportCollection();

        if (! empty($tour['duration'])) {
            $highlights->push($this->cleanText((string) $tour['duration']).' experience');
        }

        $location = $this->normalizeLocation($tour['location'] ?? '');

        if ($location) {
            $highlights->push('Located in '.$location);
        }

        $highlights = $highlights->merge(collect($tour['includes'] ?? [])->take(2)->map(
            fn ($item) => $this->cleanText((string) $item)
        ));

        $fallback = match ($category) {
            'Desert' => ['Guided desert adventure', 'Scenic Arabian desert setting'],
            'Yacht' => ['On-water Dubai hosting', 'Marina and skyline views'],
            'Water & Family' => ['Family-friendly attraction access', 'Comfort-first leisure format'],
            default => ['Guided city experience', 'Popular landmark coverage'],
        };

        return $highlights
            ->merge($fallback)
            ->filter()
            ->unique()
            ->take(4)
            ->values()
            ->all();
    }

    protected function pickupNoteForCategory(string $category): string
    {
        return match ($category) {
            'Desert' => 'Pickup details are confirmed after inquiry based on hotel location and selected timing.',
            'Yacht' => 'Boarding location and reporting time are confirmed after inquiry.',
            default => 'Transfer and meeting-point details are confirmed during planning.',
        };
    }

    protected function resolveTag(array $tour, string $category): ?string
    {
        $haystack = Str::lower(($tour['title'] ?? '').' '.($tour['slug'] ?? ''));

        if (Str::contains($haystack, ['vip', 'super yacht', 'private'])) {
            return 'Premium';
        }

        return match ($category) {
            'Desert' => 'Adventure',
            'Yacht' => 'Signature',
            'Water & Family' => 'Family',
            default => 'City',
        };
    }

    protected function isPrivate(array $tour): bool
    {
        $haystack = Str::lower(($tour['title'] ?? '').' '.($tour['slug'] ?? ''));

        return Str::contains($haystack, ['private', 'transfer', 'super yacht']);
    }

    protected function normalizeLocation(string $location): ?string
    {
        $location = $this->cleanText($location);

        if ($location === '') {
            return null;
        }

        if (Str::startsWith($location, 'http')) {
            return 'Dubai';
        }

        return Str::limit($location, 120, '');
    }

    protected function firstSentence(string $text): string
    {
        $text = $this->cleanText($text);

        if ($text === '') {
            return '';
        }

        $parts = preg_split('/(?<=[.!?])\s+/', $text);

        return $parts[0] ?? '';
    }

    protected function cleanText(string $value): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\x{00A0}+/u', ' ', $value) ?? $value;
        $value = str_replace(
            ['â€™', 'â€"', 'â€“', 'â€"', 'â€œ', 'â€', 'Â', 'Ã©', 'Ã', 'âˆ’'],
            ["'", '-', '-', '"', '"', '"', '', 'é', 'à', '-'],
            $value,
        );
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? trim($value);

        return $value;
    }
}

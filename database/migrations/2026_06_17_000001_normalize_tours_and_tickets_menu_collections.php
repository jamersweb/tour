<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('collections') || ! Schema::hasColumn('collections', 'collection_group')) {
            return;
        }

        $now = now();

        DB::table('collections')
            ->whereIn('collection_group', ['location', 'activity'])
            ->update([
                'is_featured' => false,
                'updated_at' => $now,
            ]);

        foreach ($this->menuCollections() as $collection) {
            DB::table('collections')->updateOrInsert(
                ['slug' => $collection['slug']],
                [
                    'name' => $collection['name'],
                    'collection_group' => $collection['collection_group'],
                    'summary' => $collection['summary'],
                    'sort_order' => $collection['sort_order'],
                    'is_featured' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('collections') || ! Schema::hasColumn('collections', 'collection_group')) {
            return;
        }

        DB::table('collections')
            ->whereIn('slug', collect($this->menuCollections())->pluck('slug')->all())
            ->update([
                'is_featured' => false,
                'updated_at' => now(),
            ]);
    }

    /**
     * @return array<int, array{name: string, slug: string, collection_group: string, summary: string, sort_order: int}>
     */
    private function menuCollections(): array
    {
        return [
            [
                'name' => 'Dubai',
                'slug' => 'dubai',
                'collection_group' => 'location',
                'summary' => 'Tours and tickets in Dubai.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Abu Dhabi',
                'slug' => 'abu-dhabi',
                'collection_group' => 'location',
                'summary' => 'Tours and tickets in Abu Dhabi.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Other Emirates',
                'slug' => 'other-emirates',
                'collection_group' => 'location',
                'summary' => 'Tours and tickets across the other Emirates.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Entry Tickets',
                'slug' => 'entry-tickets',
                'collection_group' => 'activity',
                'summary' => 'Attraction entry tickets and passes.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Desert Safari',
                'slug' => 'desert-safari',
                'collection_group' => 'activity',
                'summary' => 'Desert safari tours and dune experiences.',
                'sort_order' => 2,
            ],
            [
                'name' => 'City Tours',
                'slug' => 'city-tours',
                'collection_group' => 'activity',
                'summary' => 'Guided city tours and landmark routes.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Water Sports',
                'slug' => 'water-sports',
                'collection_group' => 'activity',
                'summary' => 'Water sports and marine activities.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Water Parks',
                'slug' => 'water-parks',
                'collection_group' => 'activity',
                'summary' => 'Water park tickets and family water attractions.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Theme Parks',
                'slug' => 'theme-parks',
                'collection_group' => 'activity',
                'summary' => 'Theme park tickets and entertainment attractions.',
                'sort_order' => 6,
            ],
            [
                'name' => 'Yacht & Cruises',
                'slug' => 'yacht-cruises',
                'collection_group' => 'activity',
                'summary' => 'Yacht experiences, cruises, and marina activities.',
                'sort_order' => 7,
            ],
        ];
    }
};

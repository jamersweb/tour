<?php

use App\Models\Package;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('collection_package')) {
            Schema::create('collection_package', function (Blueprint $table) {
                $table->id();
                $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
                $table->foreignId('package_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['collection_id', 'package_id']);
            });
        }

        $this->seedPackageSubcategories();
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_package');

        if (Schema::hasTable('collections') && Schema::hasColumn('collections', 'collection_group')) {
            DB::table('collections')->where('collection_group', 'package')->delete();
        }
    }

    private function seedPackageSubcategories(): void
    {
        if (! Schema::hasTable('collections') || ! Schema::hasColumn('collections', 'collection_group')) {
            return;
        }

        $now = now();

        foreach ($this->packageSubcategories() as $subcategory) {
            DB::table('collections')->updateOrInsert(
                ['slug' => $subcategory['slug']],
                [
                    'name' => $subcategory['name'],
                    'collection_group' => 'package',
                    'summary' => $subcategory['summary'],
                    'sort_order' => $subcategory['sort_order'],
                    'is_featured' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );

            $collectionId = DB::table('collections')->where('slug', $subcategory['slug'])->value('id');

            if (! $collectionId) {
                continue;
            }

            Package::query()
                ->where('is_active', true)
                ->get(['id', 'title', 'short_description', 'duration', 'location'])
                ->filter(fn (Package $package) => $this->packageMatches($package, $subcategory['slug']))
                ->values()
                ->each(function (Package $package, int $index) use ($collectionId, $now): void {
                    DB::table('collection_package')->updateOrInsert(
                        [
                            'collection_id' => $collectionId,
                            'package_id' => $package->id,
                        ],
                        [
                            'sort_order' => $index,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    );
                });
        }
    }

    private function packageMatches(Package $package, string $slug): bool
    {
        $text = strtolower(implode(' ', [
            $package->title,
            $package->short_description,
            $package->duration,
            $package->location,
        ]));

        return match ($slug) {
            'family-holidays' => preg_match('/family|kids|children|group/', $text) === 1,
            'short-stays' => preg_match('/2 days|3 days|4 days|short|weekend|3 nights|4 days/', $text) === 1,
            'event-packages' => preg_match('/event|ufc|fight|concert|weekend/', $text) === 1,
            'luxury-packages' => preg_match('/luxury|premium|decadence|private|5 star|five star/', $text) === 1,
            'budget-friendly' => preg_match('/budget|friendly|value|affordable/', $text) === 1,
            default => false,
        };
    }

    /**
     * @return array<int, array{name: string, slug: string, summary: string, sort_order: int}>
     */
    private function packageSubcategories(): array
    {
        return [
            ['name' => 'Family Holidays', 'slug' => 'family-holidays', 'summary' => 'Holiday packages suited to families, children, and groups.', 'sort_order' => 1],
            ['name' => 'Short Stays', 'slug' => 'short-stays', 'summary' => 'Short break and weekend package options.', 'sort_order' => 2],
            ['name' => 'Event Packages', 'slug' => 'event-packages', 'summary' => 'Event travel, concert, fight night, and UAE event package options.', 'sort_order' => 3],
            ['name' => 'Luxury Packages', 'slug' => 'luxury-packages', 'summary' => 'Premium and luxury holiday package options.', 'sort_order' => 4],
            ['name' => 'Budget Friendly', 'slug' => 'budget-friendly', 'summary' => 'Value-led package options for budget-aware travelers.', 'sort_order' => 5],
        ];
    }
};

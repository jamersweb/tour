<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('redirects')) {
            DB::table('redirects')->where('source_path', '/blog')->delete();
        }

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('blog_category_id')
                ->nullable()
                ->after('id')
                ->constrained('blog_categories')
                ->nullOnDelete();
        });

        $categories = DB::table('articles')
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $sortOrder = 1;

        foreach ($categories as $name) {
            $slug = $this->uniqueSlug((string) $name);

            $categoryId = DB::table('blog_categories')->insertGetId([
                'name' => $name,
                'slug' => $slug,
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('articles')
                ->where('category', $name)
                ->update([
                    'blog_category_id' => $categoryId,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blog_category_id');
        });

        Schema::dropIfExists('blog_categories');
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'blog-category';
        $slug = $baseSlug;
        $suffix = 2;

        while (DB::table('blog_categories')->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
};

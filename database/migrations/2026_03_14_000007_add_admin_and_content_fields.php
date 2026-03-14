<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('description');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->text('hero_summary')->nullable()->after('short_description');
            $table->json('highlights')->nullable()->after('description');
            $table->json('inclusions')->nullable()->after('highlights');
            $table->json('exclusions')->nullable()->after('inclusions');
            $table->string('pickup_note', 180)->nullable()->after('location');
            $table->unsignedInteger('sort_order')->default(0)->after('tag');
            $table->string('seo_title')->nullable()->after('sort_order');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'hero_summary',
                'highlights',
                'inclusions',
                'exclusions',
                'pickup_note',
                'sort_order',
                'seo_title',
                'seo_description',
            ]);
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};

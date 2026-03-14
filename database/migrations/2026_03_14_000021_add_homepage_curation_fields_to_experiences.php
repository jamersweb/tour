<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->boolean('show_on_homepage')->default(false)->after('is_featured');
            $table->unsignedInteger('homepage_sort_order')->default(0)->after('show_on_homepage');
        });

        $ids = DB::table('experiences')
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(16)
            ->pluck('id');

        foreach ($ids as $index => $id) {
            DB::table('experiences')
                ->where('id', $id)
                ->update([
                    'show_on_homepage' => true,
                    'homepage_sort_order' => $index + 1,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'show_on_homepage',
                'homepage_sort_order',
            ]);
        });
    }
};

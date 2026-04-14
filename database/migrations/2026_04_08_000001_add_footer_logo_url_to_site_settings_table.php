<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT = 'https://acutetourism.org/uploads/0000/6/2025/03/14/4-2.png';

    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('footer_logo_url')->nullable()->after('logo_url');
        });

        DB::table('site_settings')->where('id', 1)->whereNull('footer_logo_url')->update([
            'footer_logo_url' => self::DEFAULT,
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('footer_logo_url');
        });
    }
};

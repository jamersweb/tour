<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('company_legal_name')->nullable()->after('site_name');
            $table->string('organization_type')->nullable()->after('brand_name');
            $table->string('website_url')->nullable()->after('site_tagline');
            $table->string('logo_url')->nullable()->after('website_url');
            $table->json('social_links')->nullable()->after('logo_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'company_legal_name',
                'organization_type',
                'website_url',
                'logo_url',
                'social_links',
            ]);
        });
    }
};

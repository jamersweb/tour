<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NEW = 'https://acutetourism.org/uploads/0000/6/2025/03/19/5.png';

    private const OLD = 'https://acutetourism.org/uploads/0000/7/2025/04/12/logo-sin-texto.png';

    public function up(): void
    {
        DB::table('site_settings')
            ->where('id', 1)
            ->where(function ($query) {
                $query->whereNull('logo_url')
                    ->orWhere('logo_url', self::OLD);
            })
            ->update(['logo_url' => self::NEW]);
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->where('id', 1)
            ->where('logo_url', self::NEW)
            ->update(['logo_url' => self::OLD]);
    }
};

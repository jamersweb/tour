<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('experiences', 'experience_type')) {
                $table->string('experience_type', 120)->nullable()->after('pickup_note');
            }

            if (! Schema::hasColumn('experiences', 'transfer_option')) {
                $table->string('transfer_option', 120)->nullable()->after('experience_type');
            }

            if (! Schema::hasColumn('experiences', 'booking_type')) {
                $table->string('booking_type', 120)->nullable()->after('transfer_option');
            }
        });

        Schema::table('tours', function (Blueprint $table) {
            if (! Schema::hasColumn('tours', 'experience_type')) {
                $table->string('experience_type', 120)->nullable()->after('pickup_note');
            }

            if (! Schema::hasColumn('tours', 'transfer_option')) {
                $table->string('transfer_option', 120)->nullable()->after('experience_type');
            }

            if (! Schema::hasColumn('tours', 'booking_type')) {
                $table->string('booking_type', 120)->nullable()->after('transfer_option');
            }
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn([
                'experience_type',
                'transfer_option',
                'booking_type',
            ]);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'experience_type',
                'transfer_option',
                'booking_type',
            ]);
        });
    }
};

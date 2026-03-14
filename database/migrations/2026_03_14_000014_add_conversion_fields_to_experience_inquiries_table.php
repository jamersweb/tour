<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_inquiries', function (Blueprint $table) {
            $table->foreignId('experience_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('experience_title')->nullable()->after('interest');
            $table->string('source_url')->nullable()->after('source');
            $table->timestamp('contacted_at')->nullable()->after('status');
            $table->timestamp('follow_up_at')->nullable()->after('contacted_at');
            $table->text('admin_notes')->nullable()->after('follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::table('experience_inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('experience_id');
            $table->dropColumn([
                'experience_title',
                'source_url',
                'contacted_at',
                'follow_up_at',
                'admin_notes',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->timestamp('reconciled_at')->nullable()->after('confirmation_sent_at');
            $table->foreignId('reconciled_by')->nullable()->after('reconciled_at')->constrained('users')->nullOnDelete();
            $table->timestamp('refunded_at')->nullable()->after('reconciled_by');
            $table->foreignId('refunded_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reconciled_by');
            $table->dropConstrainedForeignId('refunded_by');
            $table->dropColumn([
                'reconciled_at',
                'refunded_at',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->timestamp('confirmation_sent_at')->nullable()->after('paid_at');
        });

        Schema::create('payment_transaction_travelers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_transaction_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(1);
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transaction_travelers');

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('confirmation_sent_at');
        });
    }
};

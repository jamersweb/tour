<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->string('gateway', 40)->default('network-ngenius');
            $table->string('reference')->unique();
            $table->string('status', 40)->default('pending');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->date('travel_date')->nullable();
            $table->unsignedInteger('guest_count')->nullable();
            $table->decimal('amount', 10, 2);
            $table->unsignedInteger('amount_minor');
            $table->string('currency', 3)->default('AED');
            $table->string('gateway_order_ref')->nullable()->index();
            $table->string('gateway_payment_ref')->nullable()->index();
            $table->string('payment_url')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

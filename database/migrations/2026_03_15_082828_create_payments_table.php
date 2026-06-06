<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->string('pagseguro_transaction_id')->nullable()->unique();
            $table->unsignedInteger('amount');
            $table->char('currency', 3)->default('BRL');
            $table->enum('status', ['paid', 'pending', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['credit_card', 'boleto', 'pix']);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

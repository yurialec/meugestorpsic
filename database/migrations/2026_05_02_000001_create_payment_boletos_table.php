<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_boletos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenant')->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->enum('status', ['pending', 'paid', 'cancelled', 'expired'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->dateTime('due_date');
            $table->dateTime('issuance_date');
            $table->dateTime('paid_at')->nullable();
            $table->string('barcode')->nullable();
            $table->string('digitable_line')->nullable();
            $table->string('boleto_url')->nullable();
            $table->string('external_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_boletos');
    }
};

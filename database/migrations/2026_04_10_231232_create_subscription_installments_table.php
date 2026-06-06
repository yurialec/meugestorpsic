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
        Schema::create('subscription_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('number'); // 1, 2, 3...
            $table->decimal('amount', 10, 2); // valor base da parcela
            $table->decimal('interest_amount', 10, 2)->default(0); // juros aplicados
            $table->decimal('total_amount', 10, 2); // amount + interest_amount
            $table->date('due_date'); // vencimento
            $table->timestamp('paid_at')->nullable();
            $table->string('pagseguro_charge_id')->nullable(); // ID da transação desta parcela no gateway
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamps();

            $table->unique(['subscription_id', 'number']);
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_installments');
    }
};

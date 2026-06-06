<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenant')->onDelete('cascade');
            
            $table->uuid('patient_id');
            $table->foreign('patient_id')->references('id')->on('')->onDelete('cascade');
            
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
            
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_method')->onDelete('cascade');
            
            $table->decimal('amount', 10, 2);

            $table->enum('status', ['pending', 'paid', 'cancelled', 'free'])->default('pending');
            $table->timestamp('paid_at')->nullable();

            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_financial_transactions');
    }
};

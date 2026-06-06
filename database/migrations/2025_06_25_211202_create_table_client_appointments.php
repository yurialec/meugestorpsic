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
        Schema::create('client_appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('patient_id');
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenant')->onDelete('cascade');
            $table->date('day');
            $table->time('hour');
            $table->enum('status', ['Open', 'Done', 'Canceled'])->default('Open');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['day', 'hour', 'tenant_id', 'patient_id'], 'unique_patient_appointment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('client_appointments');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};

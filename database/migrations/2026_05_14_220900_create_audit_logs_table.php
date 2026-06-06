<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('user_email')->index();
            $table->text('user_cpf')->nullable();
            $table->string('user_cpf_hash', 64)->nullable()->index();
            $table->text('patient_cpf')->nullable();
            $table->string('patient_cpf_hash', 64)->nullable()->index();
            $table->string('event_type', 50)->index();
            $table->timestampTz('occurred_at')->index();
            $table->json('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['user_email', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

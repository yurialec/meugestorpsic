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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id');
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenant')
                ->cascadeOnDelete();

            $table->uuid('patient_id');
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('client_appointment_id');
            $table->foreign('client_appointment_id')
                ->references('id')
                ->on('client_appointments')
                ->cascadeOnDelete();

            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            // CAMPOS CLÍNICOS ESPECÍFICOS
            $table->text('objectives')->nullable(); // Objetivos da sessão
            $table->text('content_worked')->nullable(); // Conteúdo trabalhado (principal)
            $table->text('clinical_observations')->nullable(); // Observações clínicas (estado emocional, comportamento, etc)
            $table->text('interventions')->nullable(); // Intervenções e técnicas utilizadas
            $table->text('planning')->nullable(); // Planejamento para próximas sessões
            $table->text('homework')->nullable(); // Tarefas/exercícios propostos (se houver)

            // CAMPOS OPCIONAIS ÚTEIS
            $table->enum('emotional_state', [
                'joy',
                'sadness',
                'anger',
                'fear',
                'disgust',
                'surprise'
            ])->nullable();
            
            $table->enum('engagement_level', ['high', 'medium', 'low'])->nullable(); // Nível de engajamento
            $table->text('insights')->nullable(); // Insights importantes da sessão


            $table->enum('status', ['open', 'done', 'canceled'])->default('open');
            $table->enum('location', ['online', 'in_person'])->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('consultations');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};

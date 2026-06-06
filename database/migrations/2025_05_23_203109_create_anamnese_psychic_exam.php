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
        Schema::create('anamnese_psychic_exam', function (Blueprint $table) {
            $table->id();
            $table->text('appearance')->nullable();
            $table->text('behavior')->nullable();
            $table->string('sense_perception', 50)->nullable();
            $table->text('thought_process')->nullable();
            $table->text('thought_content')->nullable();
            $table->text('ego_expansion')->nullable();
            $table->text('ego_retraction')->nullable();
            $table->text('ego_denial')->nullable();
            $table->text('language')->nullable();
            $table->text('affectivity')->nullable();
            $table->string('mood', 50)->nullable();
            $table->string('disease_awareness', 50)->nullable();
            $table->unsignedBigInteger('anamnese_id');
            $table->foreign('anamnese_id')
                ->references('id')
                ->on('anamnese')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anamnese_psychic_exam');
    }
};

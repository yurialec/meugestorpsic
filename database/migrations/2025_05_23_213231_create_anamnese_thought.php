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
        Schema::create('anamnese_thought', function (Blueprint $table) {
            $table->id();
            $table->boolean('accelerated')->default(false)->nullable();
            $table->boolean('slowed')->default(false)->nullable();
            $table->boolean('escape')->default(false)->nullable();
            $table->boolean('blockage')->default(false)->nullable();
            $table->boolean('wordy')->default(false)->nullable();
            $table->boolean('repetition')->default(false)->nullable();
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
        Schema::dropIfExists('anamnese_thought');
    }
};

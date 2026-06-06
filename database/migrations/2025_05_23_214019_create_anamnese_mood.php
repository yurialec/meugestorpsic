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
        Schema::create('anamnese_mood', function (Blueprint $table) {
            $table->id();
            $table->boolean('normal')->default(false)->nullable();
            $table->boolean('elated')->default(false)->nullable();
            $table->boolean('low_mood')->default(false)->nullable();
            $table->boolean('sudde_change_mood')->default(false)->nullable();
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
        Schema::dropIfExists('anamnese_mood');
    }
};

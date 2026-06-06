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
        Schema::create('anamnese_expansion_of_the_self', function (Blueprint $table) {
            $table->id();
            $table->boolean('greatness')->default(false)->nullable();
            $table->boolean('jealousy')->default(false)->nullable();
            $table->boolean('claim')->default(false)->nullable();
            $table->boolean('genealogical')->default(false)->nullable();
            $table->boolean('mystical_of_a_saving_mission')->default(false)->nullable();
            $table->boolean('deification')->default(false)->nullable();
            $table->boolean('erotic')->default(false)->nullable();
            $table->boolean('invention_or_reform')->default(false)->nullable();
            $table->boolean('fantastic_ideas')->default(false)->nullable();
            $table->boolean('excessive_health')->default(false)->nullable();
            $table->boolean('physical_capacity')->default(false)->nullable();
            $table->boolean('beauty')->default(false)->nullable();
            $table->text('others')->nullable();
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
        Schema::dropIfExists('anamnese_expansion_of_the_self');
    }
};

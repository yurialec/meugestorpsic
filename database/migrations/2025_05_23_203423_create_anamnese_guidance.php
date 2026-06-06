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
        Schema::create('anamnese_guidance', function (Blueprint $table) {
            $table->id();
            $table->boolean('self_identification')->default(false)->nullable();
            $table->boolean('body')->default(false)->nullable();
            $table->boolean('temporal')->default(false)->nullable();
            $table->boolean('spatial')->default(false)->nullable();
            $table->boolean('pathology_oriented')->default(false)->nullable();
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
        Schema::dropIfExists('anamnese_guidance');
    }
};

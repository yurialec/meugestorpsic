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
        Schema::create('anamnese_awareness_of_current_illness', function (Blueprint $table) {
            $table->id();
            $table->boolean('yes')->default(false)->nullable();
            $table->boolean('partially')->default(false)->nullable();
            $table->boolean('no')->default(false)->nullable();
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
        Schema::dropIfExists('anamnese_awareness_of_current_illness');
    }
};

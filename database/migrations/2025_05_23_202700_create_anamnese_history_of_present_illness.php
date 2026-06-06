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
        Schema::create('anamnese_history_of_present_illness', function (Blueprint $table) {
            $table->id();
            $table->text('beginning_of_the_pathology')->nullable();
            $table->text('frequency')->nullable();
            $table->text('intensity')->nullable();
            $table->text('previous_treatments')->nullable();
            $table->text('medications')->nullable();
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
        Schema::dropIfExists('anamnese_history_of_present_illness');
    }
};

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
        Schema::create('anamnese_attitude_with_the_interviewer', function (Blueprint $table) {
            $table->id();
            $table->boolean('cooperative')->default(false)->nullable();
            $table->boolean('resistant')->default(false)->nullable();
            $table->boolean('indifferent')->default(false)->nullable();
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
        Schema::dropIfExists('anamnese_attitude_with_the_interviewer');
    }
};

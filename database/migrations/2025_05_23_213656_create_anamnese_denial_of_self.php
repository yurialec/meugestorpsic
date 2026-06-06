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
        Schema::create('anamnese_denial_of_self', function (Blueprint $table) {
            $table->id();
            $table->boolean('hypochondriac')->default(false)->nullable();
            $table->boolean('denial_and_bodily_transformation')->default(false)->nullable();
            $table->boolean('self_accusation')->default(false)->nullable();
            $table->boolean('guilt')->default(false)->nullable();
            $table->boolean('ruin')->default(false)->nullable();
            $table->boolean('nihilism')->default(false)->nullable();
            $table->boolean('tendency_to_suicide')->default(false)->nullable();
            $table->text('others');
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
        Schema::dropIfExists('anamnese_denial_of_self');
    }
};

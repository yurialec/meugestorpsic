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
        Schema::create('anamnese_language', function (Blueprint $table) {
            $table->id();
            $table->boolean('dysarthria')->default(false)->nullable();
            $table->boolean('aphasia')->default(false)->nullable();
            $table->boolean('paraphasia')->default(false)->nullable();
            $table->boolean('neologism')->default(false)->nullable();
            $table->boolean('mussitation')->default(false)->nullable();
            $table->boolean('logorrhea')->default(false)->nullable();
            $table->boolean('para_responses')->default(false)->nullable();
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
        Schema::dropIfExists('anamnese_language');
    }
};

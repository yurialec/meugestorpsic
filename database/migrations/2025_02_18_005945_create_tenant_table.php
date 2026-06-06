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
        Schema::dropIfExists('tenant');
        Schema::create('tenant', function (Blueprint $table) {
            $table->id();
            $table->string('domain', 50);
            $table->enum('type', ['individual', 'clinic', 'educational_institution']);
            $table->string('logo', 255)->nullable();
            $table->integer('user_limit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tenant');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

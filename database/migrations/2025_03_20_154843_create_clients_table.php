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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('cpf')->nullable();
            $table->string('crp', 7)->unique();
            $table->string('function')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('tenant_id')->unsigned()->index()->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenant')->onDelete('cascade');
            $table->string('token')->nullable()->unique();
            $table->timestamp('token_expiration')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('clients');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

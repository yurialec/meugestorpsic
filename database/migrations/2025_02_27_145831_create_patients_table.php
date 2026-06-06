<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('full_name');
            $table->string('cpf');
            $table->string('email');
            $table->string('phone');
            $table->date('date_of_birth');

            $table->enum('group', ['child', 'adult', 'teen', 'elderly']);
            $table->enum('gender', ['F', 'M', 'other']);

            $table->bigInteger('tenant_id')->unsigned()->index()->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenant')->onDelete('cascade');

            $table->unsignedBigInteger('employee_id')->index()->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['cpf', 'tenant_id'], 'unique_cpf_per_tenant');
            $table->unique(['email', 'tenant_id'], 'unique_email_per_tenant');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('patients');
        Schema::enableForeignKeyConstraints();
    }
};

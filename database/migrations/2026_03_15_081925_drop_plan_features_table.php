<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_tier_id']);
            $table->dropColumn('plan_tier_id');
        });

        Schema::dropIfExists('plan_features');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['individual', 'clinic', 'education_institution']);
            $table->string('name')->nullable(); // ex: basic, pro, premium
            $table->unsignedInteger('user_limit')->default(1);
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }
};

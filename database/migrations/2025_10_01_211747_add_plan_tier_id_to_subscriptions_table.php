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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_tier_id')
                ->nullable()
                ->after('plan_id')
                ->constrained('plan_features')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['plan_tier_id']);
                $table->dropColumn('plan_tier_id');
            });
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            DB::statement("
                UPDATE subscriptions
                SET started_at = start_date
                WHERE started_at IS NULL AND start_date IS NOT NULL
            ");
            DB::statement("
                UPDATE subscriptions
                SET current_period_start = start_date,
                    current_period_end   = end_date
                WHERE current_period_start IS NULL AND start_date IS NOT NULL
            ");
            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('status');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }
};

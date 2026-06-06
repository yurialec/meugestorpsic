<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'plan_id')) {
                $table->foreignId('plan_id')
                    ->nullable()
                    ->after('subscription_id')
                    ->constrained('plans')
                    ->nullOnDelete();
            }
        });

        DB::statement('ALTER TABLE payments MODIFY amount DECIMAL(10,2) NOT NULL');
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'plan_id')) {
                $table->dropConstrainedForeignId('plan_id');
            }
        });

        DB::statement('ALTER TABLE payments MODIFY amount INT UNSIGNED NOT NULL');
    }
};

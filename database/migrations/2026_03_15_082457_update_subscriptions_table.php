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
            // Altera status enum
            $table->dropColumn('status');
            $table->dropColumn('payment_gateway_subscription_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // Novo enum de status
            $table->enum('status', ['active', 'canceled', 'past_due', 'trial'])
                ->default('active')
                ->after('plan_id');

            // PagSeguro
            $table->string('pagseguro_subscription_id')->nullable()->after('status');

            // Datas
            $table->timestamp('started_at')->nullable()->after('pagseguro_subscription_id');
            $table->timestamp('current_period_start')->nullable()->after('started_at');
            $table->timestamp('current_period_end')->nullable()->after('current_period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'pagseguro_subscription_id',
                'started_at',
                'current_period_start',
                'current_period_end',
            ]);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Expired', 'Canceled'])->default('Active');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('payment_gateway_subscription_id')->nullable();
        });
    }
};

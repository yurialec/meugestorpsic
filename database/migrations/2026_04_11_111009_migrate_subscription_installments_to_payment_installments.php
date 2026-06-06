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
        $installments = DB::table('subscription_installments')->get();
        foreach ($installments as $inst) {

            $payment = DB::table('payments')
                ->where('pagseguro_transaction_id', $inst->pagseguro_charge_id)
                ->first();

            // fallback (caso não encontre pelo gateway)
            if (!$payment) {
                $payment = DB::table('payments')
                    ->where('subscription_id', $inst->subscription_id)
                    ->first();
            }

            if (!$payment) {
                continue; // ou logar erro
            }

            DB::table('payment_installments')->insert([
                'payment_id' => $payment->id,
                'number' => $inst->number,
                'amount' => $inst->amount,
                'interest_amount' => $inst->interest_amount,
                'total_amount' => $inst->total_amount,
                'due_date' => $inst->due_date,
                'paid_at' => $inst->paid_at,
                'pagseguro_charge_id' => $inst->pagseguro_charge_id,
                'status' => $inst->status,
                'created_at' => $inst->created_at,
                'updated_at' => $inst->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payment_installments')->truncate();
    }
};

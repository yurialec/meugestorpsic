<?php

use App\Models\Admin\Clients;
use App\Models\Admin\CrmInteraction;
use App\Models\Admin\CrmStatus;
use App\Models\Admin\Subscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('check-end-free-trail', function () {
    $today = Carbon::now();

    DB::transaction(function () use ($today) {
        try {
            $subs = Subscription::where('end_date', '<=', $today->addDays(2))
                ->where('status', Subscription::ACTIVE)
                ->get();

            foreach ($subs as $sub) {
                if ($sub->end_date->isPast()) {
                    $sub->update(['status' => Subscription::EXPIRED]);
                }

                $client = Clients::where('tenant_id', $sub->tenant_id)->first();

                CrmInteraction::create([
                    'client_id' => $client->id,
                    'status_id' => CrmStatus::LIGACAO,
                    'observation' => 'Entrar em contato com o clinte.',
                ]);
            }
        } catch (\Exception $err) {
            Log::error('Erro ao lançar interação', ['ERRO' => $err->getMessage()]);
            throw $err;
        }
    });
})->purpose('Verificar fim do período de teste');

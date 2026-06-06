<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\ClientsRepositoryInterface;
use App\Models\Admin\Clients;
use App\Models\Admin\CrmInteraction;
use App\Models\Admin\Subscription;
use App\Models\Tenants\Tenant;
use App\Models\Tenants\ClientConfig;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientsRepository implements ClientsRepositoryInterface
{
    protected $tenant;
    protected $client;
    protected $interaction;
    protected $subscription;
    protected $config;

    public function __construct(
        Tenant $tenant,
        Clients $client,
        CrmInteraction $interaction,
        Subscription $subscription,
        ClientConfig $config
    ) {
        $this->tenant = $tenant;
        $this->client = $client;
        $this->interaction = $interaction;
        $this->subscription = $subscription;
        $this->config = $config;
    }

    public function all()
    {
        try {
            return $this->client->with(['tenant'])->paginate(10);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function create($clientData, $tenantData, $subcriptionData, $interactionData)
    {
        $client = $this->client
            ->where('email', $clientData['email'])
            ->where('cpf', $clientData['cpf'])
            ->first();

        if (!empty($client)) {
            return [
                'status' => true,
                'exists' => true,
                'client' => $client,
            ];
        }

        try {
            DB::beginTransaction();
            $tenant = $this->tenant->create($tenantData);

            $client = $this->client->create([
                'name' => $clientData['name'],
                'email' => $clientData['email'],
                'crp' => $clientData['crp'],
                'password' => $clientData['password'],
                'tenant_id' => $tenant->id,
                'cpf' => $clientData['cpf'],
                'phone' => $clientData['phone'],
            ]);

            $config = $this->config;
            $config->client_id = $client->id;
            $config->save();

            $interaction = $this->interaction->create([
                'status_id' => $interactionData['status_id'],
                'client_id' => $client->id,
            ]);

            $subscription = $this->subscription->create([
                'tenant_id'            => $tenant->id,
                'plan_id'              => $subcriptionData['plan_id'],
                'status'               => $subcriptionData['status'],
                'started_at'           => $subcriptionData['started_at'],
                'current_period_start' => $subcriptionData['current_period_start'],
                'current_period_end'   => $subcriptionData['current_period_end'],
                'payment_method'       => $subcriptionData['payment_method'],
                'amount_paid'          => $subcriptionData['amount_paid'],
            ]);

            DB::commit();

            return [
                'status' => true,
                'exists' => false,
                'client' => $client,
            ];
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('Erro ao cadastrar', ['erro' => $err->getMessage()]);

            return [
                'status' => false,
                'error' => $err->getMessage(),
            ];
        }
    }

    public function update($id, $clientData, $subcriptionData)
    {
        DB::beginTransaction();
        try {

            $client = $this->client->find($id);

            $client->name = $clientData['name'];
            $client->email = $clientData['email'];
            $client->phone = $clientData['phone'];
            $client->cpf = $clientData['cpf'];
            $client->function = $clientData['function'];
            $client->save();

            $subscription = $this->subscription->where('tenant_id', $client->tenant->id)->first();
            $subscription->plan_id = $subcriptionData['plan_id'];
            $subscription->tenant_id = $client->tenant->id;
            $subscription->start_date = $subcriptionData['start_date'];
            $subscription->end_date = $subcriptionData['end_date'];
            $subscription->status = $subcriptionData['status'];
            $subscription->payment_gateway_subscription_id = $subcriptionData['payment_gateway_subscription_id'];

            $subscription->save();

            DB::commit();
            return true;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->client->destroy($id);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        try {
            return $this->client->with(['tenant.subscription.plan'])->find($id);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }
}

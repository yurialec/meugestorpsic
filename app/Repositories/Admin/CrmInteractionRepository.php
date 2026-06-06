<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\CrmInteractionRepositoryInterface;
use App\Models\Admin\Clients;
use App\Models\Admin\CrmInteraction;
use App\Models\Admin\CrmStatus;
use Exception;
use Illuminate\Support\Facades\DB;
use Log;

class CrmInteractionRepository implements CrmInteractionRepositoryInterface
{
    protected $crmInteraction;
    protected $client;

    public function __construct(CrmInteraction $crmInteraction, Clients $client)
    {
        $this->crmInteraction = $crmInteraction;
        $this->client = $client;
    }

    public function all()
    {
        try {
            return $this->crmInteraction
                ->select('clients.id', 'clients.name as client_name', 'crm_statuses.name as status_name', 'crm_statuses.color as status_color')
                ->leftJoin('clients', 'crm_interactions.client_id', '=', 'clients.id')
                ->leftJoin('crm_statuses', 'crm_interactions.status_id', '=', 'crm_statuses.id')
                ->whereIn('crm_interactions.id', function ($query) {
                    $query->from('crm_interactions as ci')
                        ->select(DB::raw('MAX(ci.id)'))
                        ->groupBy('ci.client_id');
                })
                ->paginate();

        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        try {
            return $this->crmInteraction->with(['client', 'status'])->where('client_id', $id)->get();
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function create($interactionData)
    {
        DB::beginTransaction();
        try {
            $interaction = $this->crmInteraction->create($interactionData);
            DB::commit();
            return true;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            throw $err;
        }
    }

    public function update($id, array $data)
    {
        $model = CrmInteraction::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        return CrmInteraction::destroy($id);
    }
}
<?php

namespace App\Services\Admin;

use App\Helpers\Utils;
use App\Repositories\Admin\CrmInteractionRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Log;

class CrmInteractionService
{
    protected $CrmInteractionRepository;

    public function __construct(CrmInteractionRepository $CrmInteractionRepository)
    {
        $this->CrmInteractionRepository = $CrmInteractionRepository;
    }

    public function all()
    {
        return $this->CrmInteractionRepository->all();
    }

    public function create($data)
    {
        try {
            if (isset($data['attachment']) and !empty($data['attachment'])) {
                $file = $data['attachment'];
                $fileName = 'interaction_' . time() . '_' . $file->getClientOriginalName();

                $filePath = $file->storeAs(
                    "private/clients/{$data['client_id']}/interactions",
                    $fileName,
                    'local'
                );
            }

            $interactionData = [
                'client_id' => $data['client_id'],
                'status_id' => $data['status_id'],
                'observation' => $data['observation'] ?? null,
                'attachment' => $filePath ?? null,
                'user_id' => Auth::id(),
                'alarm' => Carbon::now()->addDays(30),
            ];

            return $this->CrmInteractionRepository->create($interactionData);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            throw $err;
        }
    }

    public function find($id)
    {
        return $this->CrmInteractionRepository->find($id);
    }
}
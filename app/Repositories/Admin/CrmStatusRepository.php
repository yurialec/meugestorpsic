<?php

namespace App\Repositories\Admin;

use App\Interfaces\Admin\CrmStatusRepositoryInterface;
use App\Models\Admin\CrmStatus;
use Exception;
use Log;

class CrmStatusRepository implements CrmStatusRepositoryInterface
{
    protected $crmStatus;

    public function __construct(CrmStatus $crmStatus)
    {
        $this->crmStatus = $crmStatus;
    }

    public function all()
    {
        try {
            return $this->crmStatus->get();
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function find($id)
    {
        try {
            return $this->crmStatus->find($id);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function create(array $data)
    {
        try {
            return $this->crmStatus->create($data);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function update($id, array $data)
    {
        try {
            $model = $this->crmStatus->find($id);
            if ($model) {
                $model->update($data);
                return $model;
            }
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->crmStatus->destroy($id);
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }
}
<?php

namespace App\Services\Admin;

use App\Repositories\Admin\CrmStatusRepository;

class CrmStatusService
{
    protected $CrmStatusRepository;

    public function __construct(CrmStatusRepository $CrmStatusRepository)
    {
        $this->CrmStatusRepository = $CrmStatusRepository;
    }

    public function all()
    {
        return $this->CrmStatusRepository->all();
    }

    public function create($data)
    {
        return $this->CrmStatusRepository->create($data);
    }

    public function find($id)
    {
        return $this->CrmStatusRepository->find($id);
    }

    public function update($id, $data)
    {
        return $this->CrmStatusRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->CrmStatusRepository->delete($id);
    }
}
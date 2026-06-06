<?php

namespace App\Services\Admin;

use App\Helpers\Utils;
use App\Repositories\Admin\PlanRepository;

class PlanService
{
    protected $PlanRepository;

    public function __construct(PlanRepository $PlanRepository)
    {
        $this->PlanRepository = $PlanRepository;
    }

    public function all()
    {
        return $this->PlanRepository->all();
    }

    public function create($data)
    {
        $planData['name'] = $data['name'];
        $planData['price'] = $data['price'];
        $planData['description'] = $data['description'];
        $planData['duration'] = $data['duration'];

        return $this->PlanRepository->create($planData);
    }

    public function delete($id)
    {
        return $this->PlanRepository->delete($id);
    }

    public function getPlanById($id)
    {
        return $this->PlanRepository->find($id);
    }

    public function update($id, $data)
    {
        $data['price'] = Utils::sanitizeCurrency($data['price']);
        return $this->PlanRepository->update($id, $data);
    }
}
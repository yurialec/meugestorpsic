<?php

namespace App\Interfaces\Tenants;

interface ConfigurationRepositoryInterface
{
    public function confirmFirstAccess();
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function storeConsultationDuration($data);
    public function storeConsultationPrice($data);
    public function availablePlans();
    public function getConfig();
    public function logoStore($data);
    public function updateProfileConfiguration(array $data);
    public function listSchedule();
}

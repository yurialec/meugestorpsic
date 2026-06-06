<?php

namespace App\Interfaces\Tenants;

interface ConsultationRepositoryInterface
{
    public function all($term);
    public function find($id);
    public function store(array $data);
    public function update($id, array $data, array $financeData, array|null $financeInstallments);
    public function delete($id);
}
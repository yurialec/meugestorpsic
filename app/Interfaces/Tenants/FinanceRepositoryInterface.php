<?php

namespace App\Interfaces\Tenants;

interface FinanceRepositoryInterface
{
    public function all(array $dataSearch);
    public function getPdf(array $dataSearch);
    public function create(array $data);
    public function billsToReciveList(array $dataSearch);
    public function registerPayment($id, array $data);
}
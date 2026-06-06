<?php

namespace App\Interfaces\Tenants;

interface EmployeeInterface
{
    public function all($term);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function disable($id);
}
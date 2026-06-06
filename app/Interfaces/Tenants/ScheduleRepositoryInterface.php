<?php

namespace App\Interfaces\Tenants;

interface ScheduleRepositoryInterface
{
    public function all();
    public function find();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
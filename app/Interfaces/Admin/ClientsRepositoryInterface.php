<?php

namespace App\Interfaces\Admin;

interface ClientsRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $userData, array $tenantData, array $subcriptionData, array $interactionData);
    public function update($id, $clientData, array $subcriptionData);
    public function delete($id);
}
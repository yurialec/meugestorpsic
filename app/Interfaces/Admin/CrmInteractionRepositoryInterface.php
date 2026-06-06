<?php

namespace App\Interfaces\Admin;

interface CrmInteractionRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $interactionData);
    public function update($id, array $data);
    public function delete($id);
}
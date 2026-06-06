<?php

namespace App\Interfaces\Tenants;

interface AppointmentRepositoryInterface
{
    public function list();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getPatients();
    public function removePatient(array $data);
    public function findForConsultation(string $idPatient, string $day, string $hour);
}
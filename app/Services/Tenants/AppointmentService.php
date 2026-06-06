<?php

namespace App\Services\Tenants;

use App\Models\Admin\Clients;
use App\Repositories\Tenants\AppointmentRepository;
use Auth;
use Carbon\Carbon;

class AppointmentService
{
    protected $AppointmentRepository;

    public function __construct(AppointmentRepository $AppointmentRepository)
    {
        $this->AppointmentRepository = $AppointmentRepository;
    }

    public function list()
    {
        return $this->AppointmentRepository->list();
    }

    public function getPatients()
    {
        return $this->AppointmentRepository->getPatients();
    }

    public function create($data)
    {
        if (!session('is_admin')) {
            $data['employee_id'] = Auth::guard('employee')->id() ?? null;
        }

        $data['day'] = Carbon::parse($data['day'])->format('Y-m-d');
        $data['hour'] = Carbon::parse($data['hour'])->format('H:i:s'); // Mantem comparacoes futuras consistentes com a coluna time.
        $data['tenant_id'] = session('tenant_id');

        return $this->AppointmentRepository->create($data);
    }

    public function removePatient($data)
    {
        $data['day'] = Carbon::parse($data['day'])->format('Y-m-d');
        $data['hour'] = Carbon::parse($data['hour'])->format('H:i:s'); // Corrige cancelamento quando o frontend envia HH:mm.
        return $this->AppointmentRepository->removePatient($data);
    }

    public function findForConsultation($idPatient, $day, $hour)
    {
       return  $this->AppointmentRepository->findForConsultation($idPatient, $day, $hour);
    }
}

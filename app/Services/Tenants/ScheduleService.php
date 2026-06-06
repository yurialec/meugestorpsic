<?php

namespace App\Services\Tenants;

use App\Models\Admin\Clients;
use App\Repositories\Tenants\ScheduleRepository;
use Auth;
use Spatie\FlareClient\Http\Client;

class ScheduleService
{
    protected $ScheduleRepository;

    public function __construct(ScheduleRepository $ScheduleRepository)
    {
        $this->ScheduleRepository = $ScheduleRepository;
    }

    public function all()
    {
        return $this->ScheduleRepository->all();
    }

    public function find()
    {
        return $this->ScheduleRepository->find();
    }

    public function create($data)
    {

        $isAdmin = session('is_admin');

        $scheduleData = [
            'tenant_id' => session('tenant_id'),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'start_break_time' => $data['start_break_time'],
            'end_break_time' => $data['end_break_time'],
            'days' => $data['selectedDays'],
        ];

        if ($isAdmin) {
            $scheduleData['client_id'] = Auth::guard('client')->id();
        } else {
            $scheduleData['employee_id'] = Auth::guard('employee')->id();
        }

        return $this->ScheduleRepository->create($scheduleData);
    }
}
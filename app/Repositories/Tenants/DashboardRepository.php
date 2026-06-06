<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\DashboardRepositoryInterface;
use App\Models\Tenants\Appointment;
use App\Models\Tenants\Patient;
use Exception;
use Log;

class DashboardRepository implements DashboardRepositoryInterface
{
    protected $patients;
    protected $appointments;

    public function __construct(Patient $patients, Appointment $appointments)
    {
        $this->patients = $patients;
        $this->appointments = $appointments;
    }

    public function graphsInfo()
    {
        try {
            $amount_patients = $this->patients->count();

            $today_appointments = $this->appointments
                ->where('day', today())
                ->where('status', Appointment::STATUS_OPEN)
                ->count();


            $next_appointment = $this->appointments
                ->where('status', Appointment::STATUS_OPEN)
                ->where('day', today())
                ->first();

            $appointmentsOpen = $this->appointments->where('status', Appointment::STATUS_OPEN)->count();
            $appointmentsDone = $this->appointments->where('status', Appointment::STATUS_DONE)->count();
            $appointmentsCanceled = $this->appointments->where('status', Appointment::STATUS_CANCELED)->count();

            $data = [
                'amount_patients' => $amount_patients,
                'today_appointments' => $today_appointments,
                'next_appointment' => $next_appointment,
                'appointments' => [
                    'open' => $appointmentsOpen,
                    'done' => $appointmentsDone,
                    'canceled' => $appointmentsCanceled,
                ]
            ];

            return $data;
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return $err->getMessage();
        }
    }

    public function getAppointmentByWeek($beginning_of_the_week, $end_of_the_week)
    {
        try {
            return $this->appointments
                ->whereBetween('day', [$beginning_of_the_week, $end_of_the_week])
                ->get();
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return false;
        }
    }
}
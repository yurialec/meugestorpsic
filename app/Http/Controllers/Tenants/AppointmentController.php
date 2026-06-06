<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Services\Tenants\AppointmentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Log;

class AppointmentController extends Controller
{
    protected $AppointmentService;

    public function __construct(AppointmentService $AppointmentService)
    {
        $this->AppointmentService = $AppointmentService;
    }

    public function index()
    {
        return view('tenant.apponitment.index');
    }

    public function getCallendar()
    {
        try {
            $todayDate = Carbon::now()->format('Y-m-d');
            $today = Carbon::now();
            $monthDays = [];

            foreach (range(1, $today->daysInMonth) as $day) {
                $monthDays[] = Carbon::createFromDate($today->year, $today->month, $day)->format('Y-m-d');
            }

            return response()->json(
                [
                    'todayDate' => $todayDate,
                    'monthDays' => $monthDays
                ]
            );
        } catch (Exception $err) {
            Log::error('Erro ao recuperar calendário', ['Erro' => $err->getMessage()]);
            return response()->json(
                [
                    'message' => $err->getMessage(),
                ],
                500
            );
        }
    }

    public function getPatients()
    {
        $patients = $this->AppointmentService->getPatients();
        if ($patients) {
            return response()->json([
                'status' => true,
                'patients' => $patients,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao listar pacientes'
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $appointment = $this->AppointmentService->create($request->all());

        if ($appointment) {
            return response()->json([
                'status' => true,
                'appointment' => $appointment,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar agendamento'
            ], 500);
        }
    }

    public function list()
    {
        $busyAppointiments = $this->AppointmentService->list();

        if ($busyAppointiments) {
            return response()->json([
                'status' => true,
                'busyAppointiments' => $busyAppointiments,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao localizar busyAppointiments'
            ], 500);
        }
    }

    public function removePatient(Request $request)
    {
        $busyAppointiments = $this->AppointmentService->removePatient($request->all());

        if ($busyAppointiments) {
            return response()->json([
                'status' => true,
                'message' => 'Removido com sucesso',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao localizar busyAppointiments'
            ], 500);
        }
    }

    public function findForConsultation($tenant, $idPatient, $day, $hour)
    {
        $appointment = $this->AppointmentService->findForConsultation($idPatient, $day, $hour);
        if ($appointment) {
            return response()->json([
                'status' => true,
                'appointment' => $appointment,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao localizar consulta'
            ], 500);
        }
    }
}
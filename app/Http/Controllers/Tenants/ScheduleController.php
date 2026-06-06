<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Schedule\ScheduleStoreUpdateRequest;
use App\Services\Tenants\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $ScheduleService;

    public function __construct(ScheduleService $ScheduleService)
    {
        $this->ScheduleService = $ScheduleService;
    }

    public function index()
    {
        return view('tenant.configuration.schedule.index');
    }

    public function find()
    {
        $schedule = $this->ScheduleService->find();

        if ($schedule) {
            return response()->json([
                'status' => true,
                'schedule' => $schedule,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'erro ao localizar registro'
            ], 500);
        }
    }

    public function list()
    {
        $schedule = $this->ScheduleService->all();

        if ($schedule) {
            return response()->json([
                'status' => true,
                'schedule' => $schedule,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'erro ao localizar registro'
            ], 500);
        }
    }

    public function create(ScheduleStoreUpdateRequest $request)
    {
        $schedule = $this->ScheduleService->create($request->all());

        if ($schedule) {
            return response()->json([
                'status' => true,
                'schedule' => $schedule,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'erro ao localizar registro'
            ], 500);
        }
    }
}
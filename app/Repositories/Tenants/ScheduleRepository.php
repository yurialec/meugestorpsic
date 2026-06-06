<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\ScheduleRepositoryInterface;
use App\Models\Tenants\EmployeeConfig;
use App\Models\Tenants\EmployeeSchedule;
use App\Models\Tenants\Schedule;
use Auth;
use DB;
use Exception;
use Log;
use Session;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    protected $schedule;
    protected $employeeConfig;
    protected $employeeSchedule;

    public function __construct(Schedule $schedule, EmployeeConfig $employeeConfig, EmployeeSchedule $employeeSchedule)
    {
        $this->schedule = $schedule;
        $this->employeeConfig = $employeeConfig;
        $this->employeeSchedule = $employeeSchedule;
    }

    public function all()
    {
        try {
            return $this->schedule->where('tenant_id', session('tenant_id'))->get();
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return $err->getMessage();
        }
    }

    public function find()
    {
        try {
            if (session('is_admin')) {
                return $this->schedule
                    ->where('tenant_id', session('tenant_id'))
                    ->get();
            } else {
                return $this->employeeConfig
                    ->where('employee_id', Auth::guard('employee')->id())
                    ->first();
            }
        } catch (Exception $err) {
            Log::error('ERRO', ['erro' => $err->getMessage()]);
            return $err->getMessage();
        }
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $foreignKey = isset($data['client_id']) ? 'client_id' : 'employee_id';
            $model = isset($data['client_id']) ? $this->schedule : $this->employeeSchedule;

            $model->where($foreignKey, $data[$foreignKey])->delete();

            $schedulesToCreate = collect($data['days'])->map(function ($day) use ($data, $foreignKey) {
                return [
                    'tenant_id' => $data['tenant_id'],
                    $foreignKey => $data[$foreignKey],
                    'day_of_week' => $day,
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'start_break_time' => $data['start_break_time'],
                    'end_break_time' => $data['end_break_time'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            $model->insert($schedulesToCreate);
            DB::commit();
            return $model->where($foreignKey, $data[$foreignKey])->get();
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('Error creating schedules', [
                'error' => $err->getMessage(),
                'data' => $data,
            ]);

            throw $err;
        }
    }

    public function update($id, array $data)
    {
        $model = Schedule::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        return Schedule::destroy($id);
    }
}
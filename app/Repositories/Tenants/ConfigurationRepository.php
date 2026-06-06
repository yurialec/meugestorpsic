<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\ConfigurationRepositoryInterface;
use App\Models\Admin\Clients;
use App\Models\Admin\Plan;
use App\Models\Tenants\Address;
use App\Models\Tenants\ClientConfig;
use App\Models\Tenants\Configuration;
use App\Models\Tenants\Employee;
use App\Models\Tenants\EmployeeConfig;
use App\Models\Tenants\EmployeeSchedule;
use App\Models\Tenants\Schedule;
use App\Models\Tenants\Tenant;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    protected $tenant;
    protected $plan;
    protected $config;

    protected $employeeConfig;
    protected $client;
    protected $employee;
    protected $address;
    protected $schedule;
    protected $employeeSchedule;

    public function __construct(
        Tenant $tenant,
        Plan $plan,
        ClientConfig $config,
        EmployeeConfig $employeeConfig,
        Clients $client,
        Employee $employee,
        Address $address,
        Schedule $schedule,
        EmployeeSchedule $employeeSchedule
    ) {
        $this->tenant = $tenant;
        $this->plan = $plan;
        $this->config = $config;
        $this->employeeConfig = $employeeConfig;
        $this->client = $client;
        $this->employee = $employee;
        $this->address = $address;
        $this->schedule = $schedule;
        $this->employeeSchedule = $employeeSchedule;
    }

    public function confirmFirstAccess()
    {
        try {
            $config = $this->config->where('client_id', Auth::guard('client')->id())->first();
            $config->first_access = Carbon::now();
            $config->save();
            return true;
        } catch (Exception $err) {
            Log::error('Erro ao localizar configurações', [$err->getMessage()]);
            return false;
        }
    }

    public function all()
    {
        return Configuration::all();
    }

    public function find($id)
    {
        return Configuration::find($id);
    }

    public function create(array $data)
    {
        return Configuration::create($data);
    }

    public function update($id, array $data)
    {
        $model = Configuration::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function storeConsultationDuration($data)
    {
        try {
            if (session('is_admin')) {
                $tenantId = session('tenant_id');
                $config = $this->config->where('client_id', Auth::guard('client')->id())->first();
                $config->consultation_duration = $data['consultation_duration'];
                $config->save();
            } else {
                $config = $this->employeeConfig
                    ->where('employee_id', Auth::guard(name: 'employee')->id())
                    ->first();
                $config->consultation_duration = $data['consultation_duration'];
                $config->save();
            }

            return $config;
        } catch (Exception $err) {
            Log::error('Erro ao cadastrar duração de atendimento', [$err->getMessage()]);
            return false;
        }
    }
    public function storeConsultationPrice($data)
    {
        try {
            if (session('is_admin')) {
                $tenantId = session('tenant_id');
                $config = $this->config->where('client_id', Auth::guard('client')->id())->first();
                $config->consultation_price = $data['consultation_price'];
                $config->save();
            } else {
                $config = $this->employeeConfig
                    ->where('employee_id', Auth::guard(name: 'employee')->id())
                    ->first();
                $config->consultation_price = $data['consultation_price'];
                $config->save();
            }

            return $config;
        } catch (Exception $err) {
            Log::error('Erro ao cadastrar valor atendimento', [$err->getMessage()]);
            return false;
        }
    }

    public function availablePlans()
    {
        try {
            return $this->plan->get();
        } catch (Exception $err) {
            Log::error('Erro ao listar planos', [$err->getMessage()]);
            return false;
        }
    }

    public function getConfig()
    {
        try {
            if (session('is_admin')) {
                return $this->config
                    ->where('client_id', Auth::guard('client')->id())
                    ->first();
            } else {
                return $this->employeeConfig
                    ->where('employee_id', Auth::guard(name: 'employee')->id())
                    ->first();
            }
        } catch (Exception $err) {
            Log::error('Erro ao localizar configuração', [$err->getMessage()]);
            return false;
        }
    }

    public function logoStore($data)
    {
        try {
            $tenant = $this->tenant->find(session('tenant_id'));
            $tenant->logo = $data['logo'] ?? null;
            $tenant->save();
            return $tenant;
        } catch (Exception $err) {
            Log::error('Erro ao cadastrar logotipo', [$err->getMessage()]);
            return false;
        }
    }

    public function updateProfileConfiguration(array $data)
    {
        DB::beginTransaction();

        try {
            $tenantId = session('tenant_id');
            $isAdmin = session('is_admin');
            $user = $isAdmin
                ? $this->client->findOrFail(Auth::guard('client')->id())
                : $this->employee->findOrFail(Auth::guard('employee')->id());

            $user->fill(array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'] ?? null,
            ], fn($value) => !is_null($value)));
            $user->save();

            $configModel = $isAdmin
                ? $this->config->firstOrNew(['client_id' => $user->id])
                : $this->employeeConfig->firstOrNew(['employee_id' => $user->id]);

            $configModel->consultation_duration = $data['consultation_duration'] ?? null;
            $configModel->consultation_price = $data['consultation_price'] ?? null;
            $configModel->save();

            if (isset($data['logo_path'])) {
                $tenant = $this->tenant->findOrFail($tenantId);
                $tenant->logo = $data['logo_path'];
                $tenant->save();
            }

            if (array_key_exists('address', $data)) {
                $address = $data['address'] ?? [];
                $this->address->updateOrCreate(
                    ['tenant_id' => $tenantId],
                    [
                        'street' => $address['street'] ?? '',
                        'number' => $address['number'] ?? null,
                        'complement' => $address['complement'] ?? null,
                        'neighborhood' => $address['neighborhood'] ?? null,
                        'city' => $address['city'] ?? '',
                        'state' => strtoupper($address['state'] ?? ''),
                        'postal_code' => $address['postal_code'] ?? '',
                    ]
                );
            }

            if (array_key_exists('schedule', $data)) {
                $this->syncSchedules($data['schedule'] ?? [], $tenantId, $user->id, $isAdmin);
            }

            DB::commit();

            return $isAdmin
                ? $this->client
                ->where('id', $user->id)
                ->with(['config', 'tenant.address', 'schedules'])
                ->first()
                : $this->employee
                ->where('id', $user->id)
                ->with(['config', 'tenant.address', 'schedules'])
                ->first();
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('Erro ao salvar configurações do perfil', [$err->getMessage()]);
            return false;
        }
    }

    private function syncSchedules(array $scheduleData, $tenantId, $userId, bool $isAdmin): void
    {
        $days = $scheduleData['selectedDays'] ?? [];
        $model = $isAdmin ? $this->schedule : $this->employeeSchedule;
        $foreignKey = $isAdmin ? 'client_id' : 'employee_id';

        $model->where($foreignKey, $userId)->delete();

        if (empty($days)) {
            return;
        }

        $schedules = collect($days)->map(function ($day) use ($scheduleData, $tenantId, $userId, $foreignKey, $isAdmin) {
            $startBreakTime = ($scheduleData['start_break_time'] ?? '') ?: ($isAdmin ? '00:00' : null);
            $endBreakTime = ($scheduleData['end_break_time'] ?? '') ?: ($isAdmin ? '00:00' : null);

            return [
                'tenant_id' => $tenantId,
                $foreignKey => $userId,
                'day_of_week' => $day,
                'start_time' => $scheduleData['start_time'] ?? '00:00',
                'end_time' => $scheduleData['end_time'] ?? '23:59',
                'start_break_time' => $startBreakTime,
                'end_break_time' => $endBreakTime,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $model->insert($schedules);
    }

    public function listSchedule()
    {
        try {
            $tenantId = session('tenant_id');
            $schedule = $this->schedule->where('tenant_id', $tenantId)->get();
            return $schedule;
        } catch (Exception $err) {
            Log::error('Erro ao listar planos', [$err->getMessage()]);
            return false;
        }
    }
}

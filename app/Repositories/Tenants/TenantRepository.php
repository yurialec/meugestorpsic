<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\TenantRepositoryInterface;
use App\Models\Admin\Clients;
use App\Models\Tenants\Employee;
use App\Models\Tenants\Patient;
use App\Models\Tenants\Tenant;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TenantRepository implements TenantRepositoryInterface
{
    protected $client;
    protected $tenant;
    protected $employee;
    protected $patients;

    public function __construct(Clients $client, Tenant $tenant, Employee $employee, Patient $patients)
    {
        $this->client = $client;
        $this->tenant = $tenant;
        $this->employee = $employee;
        $this->patients = $patients;
    }

    public function all()
    {
        return Tenant::all();
    }

    public function find($id)
    {
        return Tenant::find($id);
    }

    public function create(array $data)
    {
        return Tenant::create($data);
    }

    public function update($id, array $data)
    {
        $model = Tenant::find($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        return Tenant::destroy($id);
    }

    public function viewProfile()
    {
        try {
            if (session('is_admin')) {
                return $this->client
                    ->where('tenant_id', session('tenant_id'))
                    ->with([
                        'config',
                        'tenant',
                        'schedules',
                        'tenant.address'
                    ])
                    ->first();
            } else {
                return $this->employee
                    ->with(['config', 'schedules'])
                    ->where('id', Auth::guard('employee')->id())
                    ->first();
            }
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function updateProfile($id, $data): object|null
    {
        try {
            $client = $this->client->findOrFail($id);

            $fillable = ['name', 'email', 'phone', 'function', 'password'];

            foreach ($fillable as $field) {
                if (array_key_exists($field, $data) && !is_null($data[$field])) {
                    $client->{$field} = $data[$field];
                }
            }

            $client->updated_at = Carbon::now();
            $client->save();

            return $client;
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getTenantData()
    {
        try {
            return $this->tenant->find(session('tenant_id'));
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function birthdaysOfTheMonth()
    {
        try {
            return $this->patients
                ->select(['full_name', 'date_of_birth'])
                ->where('tenant_id', session('tenant.id'))
                ->whereMonth('date_of_birth', now()->month)
                ->get();
        } catch (Exception $e) {
            Log::error('Erro', ['error' => $e->getMessage()]);
            return null;
        }
    }
}

<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\TenantLoginRequest;
use App\Models\Admin\Clients;
use App\Models\Tenants\ClientConfig;
use App\Models\Tenants\Employee;
use App\Models\Tenants\EmployeeConfig;
use App\Models\Tenants\Tenant;
use Auth;
use Exception;
use Illuminate\Http\Request;

class AuthTenantController extends Controller
{
    public function showLoginForm()
    {
        return view('tenant.auth.index');
    }

    public function login(TenantLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        $email = $credentials['email'];

        try {
            $employee = Employee::with('tenant')->where('email', $email)->first();
            if ($employee) {
                if (Auth::guard('employee')->attempt($credentials, $remember)) {
                    $request->session()->regenerate();

                    $user = Auth::guard('employee')->user();
                    $tenant = $user->tenant ?? Tenant::find($user->tenant_id);

                    if (!$tenant) {
                        Auth::guard('employee')->logout();
                        return back()
                            ->withErrors(['email' => 'Funcionário não associado a um tenant válido.'])
                            ->withInput($request->only('email'));
                    }
                    $employeeConfig = EmployeeConfig::where('employee_id', $user->id)->first();

                    session()->put('employee', [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'cpf' => $user->cpf ?? null,
                        'crp' => $user->crp ?? null,
                        'function' => $user->function ?? null,
                        'phone' => $user->phone ?? null,
                        'consultation_price' => $employeeConfig->consultation_price,
                        'consultation_duration' => $employeeConfig->consultation_duration,
                    ]);

                    session([
                        'tenant_domain' => $tenant->domain,
                        'tenant_id' => $tenant->id,
                        'tenant' => $tenant->toArray(),
                        'is_admin' => false,
                    ]);

                    return redirect()->intended(route('tenant.dashboard', ['tenant' => $tenant->domain]));
                }

                return back()
                    ->withErrors(['email' => __('auth.failed')])
                    ->withInput($request->only('email'));
            }

            $client = Clients::with('tenant')->where('email', $email)->first();
            if ($client) {
                if (!Auth::guard('client')->attempt($credentials, $remember)) {
                    return back()
                        ->withErrors(['email' => __('auth.failed')])
                        ->withInput($request->only('email'));
                }

                $request->session()->regenerate();

                if (!$client->tenant) {
                    Auth::guard('client')->logout();
                    return back()
                        ->withErrors(['email' => 'Cliente não associado a um tenant válido.'])
                        ->withInput($request->only('email'));
                }

                $tenant = $client->tenant;
                $clientConfig = ClientConfig::where('client_id', $client->id)->first();

                session()->put('client', [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'cpf' => $client->cpf ?? null,
                    'crp' => $client->crp ?? null,
                    'function' => $client->function ?? null,
                    'phone' => $client->phone ?? null,
                    'consultation_price' => $clientConfig->consultation_price,
                    'consultation_duration' => $clientConfig->consultation_duration,
                ]);

                session([
                    'tenant_domain' => $tenant->domain,
                    'tenant_id' => $tenant->id,
                    'tenant' => $tenant->toArray(),
                    'is_admin' => true,
                ]);

                return redirect()->intended(route('tenant.dashboard', ['tenant' => $tenant->domain]));
            }

            return back()
                ->withErrors(['email' => __('auth.failed')])
                ->withInput($request->only('email'));

        } catch (Exception $e) {
            Auth::guard('client')->logout();
            Auth::guard('employee')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login' => 'Ocorreu um erro ao processar o login. Por favor, tente novamente.']);
        }
    }


    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('tenant.login.form');
    }
}

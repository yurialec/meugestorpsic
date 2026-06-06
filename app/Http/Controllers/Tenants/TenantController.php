<?php

namespace App\Http\Controllers\Tenants;
use App\Http\Controllers\Controller;
use App\Services\Tenants\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected $TenantService;

    public function __construct(TenantService $TenantService)
    {
        $this->TenantService = $TenantService;
    }

    public function getMenu(): array
    {
        $list = [];

        $list[] = [
            'url' => route('tenant.dashboard', ['tenant' => session('tenant_domain')]),
            'icon' => 'nav-icon fas fa-house',
            'name' => 'Home',
            'submenu' => [],
        ];

        if (session('is_admin')) {
            $list[] = [
                'url' => route('tenant.subscription.index', ['tenant' => session('tenant_domain')]),
                'icon' => 'nav-icon fa-solid fa-sack-dollar',
                'name' => 'Assinatura',
                'submenu' => [],
            ];
        }

        if (session('is_admin')) {
            $list[] = [
                'name' => 'Financeiro',
                'icon' => 'nav-icon fa-solid fa-file-invoice-dollar',
                'url' => '#',
                'submenu' => [
                    [
                        'name' => 'Contas a Receber',
                        'icon' => 'fa-solid fa-arrow-down-short-wide',
                        'url' => route('tenant.finance.billsToReciveIndex', ['tenant' => session('tenant_domain')]),
                    ],
                    // [
                    //     'name' => 'Contas a Pagar',
                    //     'icon' => 'fa-solid fa-arrow-up-short-wide',
                    //     'url' => route('tenant.finance.billsToPayIndex', ['tenant' => session('tenant_domain')]),
                    // ],
                    [
                        'name' => 'Relatório Geral',
                        'icon' => 'fa-solid fa-chart-line',
                        'url' => route('tenant.finance.index', ['tenant' => session('tenant_domain')]),
                    ],
                ],
            ];
        }

        // if (session('is_admin')) {
        //     $list[] = [
        //         'url' => route('tenant.employee.index', ['tenant' => session('tenant_domain')]),
        //         'icon' => 'nav-icon fa-solid fa-users',
        //         'name' => 'Usuários',
        //         'submenu' => [],
        //     ];
        // }

        $list[] = [
            'url' => route('tenant.patient.index', ['tenant' => session('tenant_domain')]),
            'icon' => 'nav-icon fas fa-user-injured',
            'name' => 'Paciente',
            'submenu' => [],
        ];

        $list[] = [
            'url' => route('tenant.appointment.index', ['tenant' => session('tenant_domain')]),
            'icon' => 'nav-icon fas fa-calendar-alt',
            'name' => 'Agenda',
            'submenu' => [],
        ];

        $list[] = [
            'url' => route('tenant.consultation.index', ['tenant' => session('tenant_domain')]),
            'icon' => 'nav-icon fa-regular fa-rectangle-list',
            'name' => 'Consultas',
            'submenu' => [],
        ];

        // if (session('is_admin')) {
        //     $list[] = [
        //         'url' => route('tenant.configuration.index', ['tenant' => session('tenant_domain')]),
        //         'icon' => 'nav-icon fas fa-cogs',
        //         'name' => 'Configurações',
        //         'submenu' => [],
        //     ];
        // }

        $list[] = [
            'url' => route('tenant.logout', ['tenant' => session('tenant_domain')]),
            'icon' => 'nav-icon fas fa-sign-out-alt',
            'name' => 'Sair',
            'submenu' => [],
        ];

        return $list;
    }

    public function getTenantData()
    {
        $data = $this->TenantService->getTenantData();

        if ($data) {
            return response()->json([
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao localizar dados!'
            ], 500);
        }
    }

    public function profile()
    {
        return view('tenant.profile.index');
    }

    public function viewProfile()
    {
        $profile = $this->TenantService->viewProfile();

        if ($profile) {
            return response()->json([
                'profile' => $profile
            ], 200);
        } else {
            return response()->json([
                'message' => $profile
            ], 500);
        }
    }

    public function updateProfile($tenant, $id, Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:15'],
            'function' => ['nullable', 'string', 'max:255'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[@$!%*?&])/',
                'confirmed'
            ];
        }

        $messages = [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail informado não é válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos uma letra, um número e um caractere especial (@$!%*?&).',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $profile = $this->TenantService->updateProfile($id, $validatedData);

        if ($profile) {
            return response()->json([
                'profile' => $profile
            ], 200);
        } else {
            return response()->json([
                'message' => $profile
            ], 500);
        }
    }

    public function birthdaysOfTheMonth()
    {
        $items = $this->TenantService->birthdaysOfTheMonth();

        if ($items) {
            return response()->json([
                'items' => $items
            ], 200);
        } else {
            return response()->json([
                'message' => 'Erro ao localizar aniversariantes'
            ], 500);
        }
    }
}
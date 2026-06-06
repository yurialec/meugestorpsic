<?php

use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\CrmInteractionController;
use App\Http\Controllers\Admin\CrmStatusController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Audit\AuditLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\LogoController;
use App\Http\Controllers\Site\MainTextController;
use App\Http\Controllers\Site\SiteAboutController;
use App\Http\Controllers\Site\SiteCarouselController;
use App\Http\Controllers\Site\SocialMediaController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Tenants\AnamneseController;
use App\Http\Controllers\Tenants\AppointmentController;
use App\Http\Controllers\Tenants\AuthTenantController;
use App\Http\Controllers\Tenants\AuthTenantForgotPasswordController;
use App\Http\Controllers\Tenants\AuthTenantResetPasswordController;
use App\Http\Controllers\Tenants\ConfigurationController;
use App\Http\Controllers\Tenants\ConsultationController;
use App\Http\Controllers\Tenants\EmployeeController;
use App\Http\Controllers\Tenants\FinanceController;
use App\Http\Controllers\Tenants\PatientController;
use App\Http\Controllers\Tenants\ScheduleController;
use App\Http\Controllers\Tenants\TenantController;
use App\Http\Controllers\Tenants\TenantDashboardController;
use App\Http\Controllers\Tenants\TenantResetPasswordController;
use App\Http\Controllers\Tenants\TenantSubscriptionController;
use App\Http\Controllers\Tenants\TenantUsersController;
use App\Models\Admin\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', [SiteController::class, 'index'])->name('index.site');
Route::get('/sobre', [SiteController::class, 'about'])->name('about');
Route::get('/contato', [SiteController::class, 'contact'])->name('contact');
Route::get('/faca-parte', [SiteController::class, 'formFacaParte'])->name('form-faca-parte');
Route::post('/faca-parte', [SiteController::class, 'facaParte'])->name('faca-parte');

Route::get('/area-restrita', [LoginController::class, 'areaRestrita'])->name('area.restrita');
Route::post('/area-restrita-login', [LoginController::class, 'login'])->name('area.restrita.login');

Route::get('/login', [AuthTenantController::class, 'showLoginForm'])->name('tenant.login.form');
Route::post('/login', [AuthTenantController::class, 'login'])->name('tenant.login');

Route::get('/enviar-link', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('usuario.password.request');
Route::post('/enviar-link', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('usuario.password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('usuario.password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('usuario.password.update');

Route::get('/client/enviar-link', [AuthTenantForgotPasswordController::class, 'showEmailForm'])->name('tenant.password.request');
Route::post('/client/enviar-link', [AuthTenantForgotPasswordController::class, 'sendResetLinkEmail'])->name('tenant.password.email');
Route::get('/client/reset-password/{token}', [AuthTenantForgotPasswordController::class, 'showResetForm'])->name('tenant.password.reset');
Route::post('/client/reset', [AuthTenantForgotPasswordController::class, 'reset'])->name('tenant.password.update');

Route::middleware(['auth'])->group(function () {

    Route::prefix('admin/')->group(function () {
        //dont need acl
        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::get('sidebar', [MenuController::class, 'sidebar'])->name('sidebar');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
        Route::get('/profile-view', [UserController::class, 'profileView'])->name('profile.view');
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs.index');
        Route::get('/audit-logs/export-csv', [AuditLogController::class, 'exportCsv'])->name('audit.logs.exportCsv');

        Route::middleware(['acl:manter-site'])->group(function () {
            Route::prefix('site')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\SiteController::class, 'index'])->name('site.index');
                Route::get('/list', [App\Http\Controllers\Admin\SiteController::class, 'list'])->name('site.list');
                Route::get('/edit', [App\Http\Controllers\Admin\SiteController::class, 'edit'])->name('site.edit');
                Route::post('/save', [App\Http\Controllers\Admin\SiteController::class, 'save'])->name('site.save');
            });
        });

        Route::middleware(['acl:manter-usuarios'])->group(function () {
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('users.index');
                Route::get('/list', [UserController::class, 'list'])->name('users.list');
                Route::get('/create', [UserController::class, 'create'])->name('users.create');
                Route::post('/store', [UserController::class, 'store'])->name('users.store');
                Route::get('/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
                Route::get('/find/{id}', [UserController::class, 'find'])->name('users.find');
                Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');
                Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
            });
        });

        Route::middleware(['acl:manter-perfis'])->group(callback: function () {
            Route::prefix('roles')->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('roles.index');
                Route::get('/list', [RoleController::class, 'list'])->name('roles.list');
                Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
                Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
                Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
                Route::get('/find/{id}', [RoleController::class, 'find'])->name('roles.find');
                Route::post('/update/{id}', [RoleController::class, 'update'])->name('roles.update');
                Route::delete('/delete/{id}', [RoleController::class, 'delete'])->name('roles.delete');
                Route::get('/list-permissions', [RoleController::class, 'listPermissions'])->name('roles.list.permissions');
            });
        });

        Route::middleware(['acl:manter-permissoes'])->group(callback: function () {
            Route::prefix('permissions')->group(function () {
                Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
                Route::get('/list', [PermissionController::class, 'list'])->name('permissions.list');
                Route::get('/create', [PermissionController::class, 'create'])->name('permissions.create');
                Route::post('/store', [PermissionController::class, 'store'])->name('permissions.store');
                Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
                Route::post('/update/{id}', [PermissionController::class, 'update'])->name('permissions.update');
                Route::delete('/delete/{id}', [PermissionController::class, 'delete'])->name('permissions.delete');
            });
        });

        Route::middleware(['acl:manter-menus'])->group(callback: function () {
            Route::prefix('menu')->group(function () {
                Route::get('/', [MenuController::class, 'index'])->name('menu.index');
                Route::get('/list', [MenuController::class, 'list'])->name('menu.list');
                Route::get('/create', [MenuController::class, 'create'])->name('menu.create');
                Route::post('/store', [MenuController::class, 'store'])->name('menu.store');
                Route::get('/edit/{id}', [MenuController::class, 'edit'])->name('menu.edit');
                Route::get('/find/{id}', [MenuController::class, 'find'])->name('menu.find');
                Route::post('/update/{id}', [MenuController::class, 'update'])->name('menu.update');
                Route::delete('/delete/{id}', [MenuController::class, 'delete'])->name('menu.delete');
                Route::post('/change-order-menu/{id}', [MenuController::class, 'changeOrderMenu'])->name('menu.changeOrderMenu');
            });
        });

        Route::prefix('crm/')->group(function () {

            Route::middleware(['acl:manter-clientes'])->group(callback: function () {
                Route::prefix('clients')->group(function () {
                    Route::get('/', [ClientsController::class, 'index'])->name('clients.index');
                    Route::get('/list', [ClientsController::class, 'list'])->name('clients.list');
                    Route::get('/create', [ClientsController::class, 'create'])->name('clients.create');
                    Route::post('/store', [ClientsController::class, 'store'])->name('clients.store');
                    Route::get('/edit/{id}', [ClientsController::class, 'edit'])->name('clients.edit');
                    Route::get('/find/{id}', [ClientsController::class, 'find'])->name('clients.find');
                    Route::put('/update/{id}', [ClientsController::class, 'update'])->name('clients.update');
                    Route::delete('/delete/{id}', [ClientsController::class, 'delete'])->name('clients.delete');
                    Route::get('/download-importation-model', [ClientsController::class, 'downloadImportationModel'])->name('clients.downloadImportationModel');
                });
            });

            Route::middleware(['acl:manter-crm-status'])->group(callback: function () {
                Route::prefix('status')->group(function () {
                    Route::get('/', [CrmStatusController::class, 'index'])->name('status.index');
                    Route::get('/list', [CrmStatusController::class, 'list'])->name('status.list');
                    Route::get('/create', [CrmStatusController::class, 'create'])->name('status.create');
                    Route::post('/store', [CrmStatusController::class, 'store'])->name('status.store');
                    Route::get('/edit/{id}', [CrmStatusController::class, 'edit'])->name('status.edit');
                    Route::get('/find/{id}', [CrmStatusController::class, 'find'])->name('status.find');
                    Route::put('/update/{id}', [CrmStatusController::class, 'update'])->name('status.update');
                    Route::delete('/delete/{id}', [CrmStatusController::class, 'delete'])->name('status.delete');
                });

                Route::prefix('interactions')->group(function () {
                    Route::get('/', [CrmInteractionController::class, 'index'])->name('interactions.index');
                    Route::get('/list', [CrmInteractionController::class, 'list'])->name('interactions.list');
                    Route::post('/download', [CrmInteractionController::class, 'downloadFile'])->name('interaction.download');
                    Route::get('/create', [CrmInteractionController::class, 'create'])->name('interactions.create');
                    Route::post('/store', [CrmInteractionController::class, 'store'])->name('interactions.store');
                    Route::get('/edit/{id}', [CrmInteractionController::class, 'edit'])->name('interactions.edit');
                    Route::get('/find/{id}', [CrmInteractionController::class, 'find'])->name('interactions.find');
                    Route::put('/update/{id}', [CrmInteractionController::class, 'update'])->name('interactions.update');
                    Route::delete('/delete/{id}', [CrmInteractionController::class, 'delete'])->name('interactions.delete');
                });
            });
        });

        Route::prefix('financial/')->group(function () {
            Route::middleware(['acl:manter-planos'])->group(callback: function () {
                Route::prefix('plan')->group(function () {
                    Route::get('/', [PlanController::class, 'index'])->name('plan.index');
                    Route::get('/list', [PlanController::class, 'list'])->name('plan.list');
                    Route::get('/create', [PlanController::class, 'create'])->name('plan.create');
                    Route::post('/store', [PlanController::class, 'store'])->name('plan.store');
                    Route::get('/edit/{id}', [PlanController::class, 'edit'])->name('plan.edit');
                    Route::get('/get-plan-by-id/{id}', [PlanController::class, 'getPlanById'])->name('clients.getplanbyid');
                    Route::put('/update/{id}', [PlanController::class, 'update'])->name('plan.update');
                    Route::delete('/delete/{id}', [PlanController::class, 'delete'])->name('plan.delete');
                });
            });

            Route::middleware(['acl:manter-inscricoes'])->group(callback: function () {
                Route::prefix('subscriptions')->group(function () {
                    Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions.index');
                    Route::get('/list', [SubscriptionController::class, 'list'])->name('subscriptions.list');
                    Route::get('/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
                    Route::post('/store', [SubscriptionController::class, 'store'])->name('subscriptions.store');
                    Route::get('/edit/{id}', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
                    Route::post('/update/{id}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
                    Route::delete('/delete/{id}', [SubscriptionController::class, 'delete'])->name('subscriptions.delete');
                });
            });

            Route::middleware(['acl:manter-pagamentos'])->group(callback: function () {
                Route::prefix('payments')->group(function () {
                    Route::get('/', [PaymentsController::class, 'index'])->name('payments.index');
                    Route::get('/list', [PaymentsController::class, 'list'])->name('payments.list');
                    Route::get('/create', [PaymentsController::class, 'create'])->name('payments.create');
                    Route::post('/store', [PaymentsController::class, 'store'])->name('payments.store');
                    Route::get('/edit/{id}', [PaymentsController::class, 'edit'])->name('payments.edit');
                    Route::post('/update/{id}', [PaymentsController::class, 'update'])->name('payments.update');
                    Route::delete('/delete/{id}', [PaymentsController::class, 'delete'])->name('payments.delete');
                });
            });
        });
    });
});

Route::get('/cep/{cep}', function ($cep) {
    $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
    return $response->json();
});

Route::get('/debug/pagseguro-credentials', function () {
    $debugToken = (string) config('pagseguro.webhook_token');
    $receivedToken = (string) request()->query('token', '');

    abort_unless(
        app()->environment('local') || ($debugToken !== '' && hash_equals($debugToken, $receivedToken)),
        403
    );

    $baseUrl = rtrim((string) config('pagseguro.base_url'), '/');
    $configuredToken = (string) config('pagseguro.token');
    $trimmedToken = trim($configuredToken, " \t\n\r\0\x0B\"'");
    $hadBearerPrefix = stripos($trimmedToken, 'Bearer ') === 0;
    $normalizedToken = $hadBearerPrefix ? trim(substr($trimmedToken, 7)) : $trimmedToken;
    $publicKey = trim((string) config('pagseguro.public_key'), " \t\n\r\0\x0B\"'");
    $webhookUrl = (string) config('pagseguro.webhook_url');
    $apiCheck = null;

    if ($baseUrl !== '' && $normalizedToken !== '') {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $normalizedToken,
            ])
                ->acceptJson()
                ->timeout(20)
                ->withoutVerifying()
                ->get($baseUrl . '/orders/ORDE_INVALID_AUTH_TEST');

            $apiCheck = [
                'url' => $baseUrl . '/orders/ORDE_INVALID_AUTH_TEST',
                'status' => $response->status(),
                'body' => $response->json(),
                'authenticated' => in_array($response->status(), [200, 400, 404], true),
                'interpretation' => match ($response->status()) {
                    401 => 'Token rejeitado pelo PagSeguro para este ambiente/API.',
                    400, 404 => 'Autenticacao aceita; o pedido de teste e invalido/inexistente.',
                    200 => 'Autenticacao aceita, mas o pedido de teste retornou sucesso inesperado.',
                    default => 'Resposta inesperada; verifique body/status.',
                },
            ];
        } catch (\Throwable $e) {
            $apiCheck = [
                'url' => $baseUrl . '/orders/ORDE_INVALID_AUTH_TEST',
                'status' => null,
                'error' => $e->getMessage(),
                'interpretation' => 'Falha de conexao ou erro local antes da resposta do PagSeguro.',
            ];
        }
    }

    return response()->json([
        'config' => [
            'base_url' => $baseUrl,
            'environment_detected' => str_contains($baseUrl, 'sandbox') ? 'sandbox' : 'production',
            'sandbox_configured' => filter_var(config('pagseguro.sandbox'), FILTER_VALIDATE_BOOLEAN),
            'production_url_ok' => $baseUrl === 'https://api.pagseguro.com',
        ],
        'token' => [
            'configured' => $configuredToken !== '',
            'raw_length_from_config' => strlen($configuredToken),
            'normalized_length' => strlen($normalizedToken),
            'had_bearer_prefix_in_env' => $hadBearerPrefix,
            'has_inner_whitespace' => preg_match('/\s/', $normalizedToken) === 1,
            'sha256_prefix' => $normalizedToken !== '' ? substr(hash('sha256', $normalizedToken), 0, 12) : null,
        ],
        'public_key' => [
            'configured' => $publicKey !== '',
            'length' => strlen($publicKey),
            'sha256_prefix' => $publicKey !== '' ? substr(hash('sha256', $publicKey), 0, 12) : null,
        ],
        'webhook' => [
            'configured' => $webhookUrl !== '',
            'host' => parse_url($webhookUrl, PHP_URL_HOST),
        ],
        'api_check' => $apiCheck,
    ]);
})->name('debug.pagseguro.credentials');

Route::prefix('{tenant}')->group(function () {
    Route::middleware(['AuthTenant'])->group(function () {

        Route::get('/get-menu', [TenantController::class, 'getMenu'])->name('tenant.getmenu');
        Route::get('/birthdays-of-the-month', [TenantController::class, 'birthdaysOfTheMonth'])->name('tenant.birthdaysOfTheMonth');
        Route::get('/profile', [TenantController::class, 'profile'])->name('tenant.profile');
        Route::get('/view-profile', [TenantController::class, 'viewProfile'])->name('tenant.viewProfile');
        Route::get('/logout', [AuthTenantController::class, 'logout'])->name('tenant.logout');
        Route::get('/configuration/available-plans', [ConfigurationController::class, 'availablePlans'])->name('availablePlans');
        Route::get('/get-tenant-data', [TenantController::class, 'getTenantData'])->name('tenant.getTenantData');
        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('tenant.paymentMethods.index');
        Route::put('/configuration/update', [ConfigurationController::class, 'update'])->name('tenant.configuration.update');
        
        Route::middleware(['ValidateTenantSub'])->group(function () {

            Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');
            Route::get('/dashboard/graphs-info', [TenantDashboardController::class, 'graphsInfo'])->name('tenant.graphsInfo');
            Route::post('/dashboard/get-appointment-by-week', [TenantDashboardController::class, 'getAppointmentByWeek'])->name('tenant.getAppointmentByWeek');
            Route::post('/update-profile/{id}', [TenantController::class, 'updateProfile'])->name(name: 'tenant.updateProfile');

            Route::prefix('finance')->group(function () {
                Route::get('/', [FinanceController::class, 'index'])->name('tenant.finance.index');
                Route::post('/list', [FinanceController::class, 'list'])->name('tenant.finance.list');
                Route::post('/get-pdf', [FinanceController::class, 'getPdf'])->name('tenant.finance.getPdf');

                Route::get('/bills-to-pay', [FinanceController::class, 'billsToPayIndex'])->name('tenant.finance.billsToPayIndex');

                Route::prefix('bills-to-recive')->group(function () {
                    Route::get('/', [FinanceController::class, 'billsToReciveIndex'])->name('tenant.finance.billsToReciveIndex');
                    Route::post('/list', [FinanceController::class, 'billsToReciveList'])->name('tenant.finance.billsToReciveList');
                    Route::post('/register-payment/{id}', [FinanceController::class, 'registerPayment'])->name('tenant.finance.registerPayment');
                });
            });

            Route::prefix('subscription')->group(function () {
                Route::get('/', [TenantSubscriptionController::class, 'index'])->name('tenant.subscription.index');
                Route::get('/find', [TenantSubscriptionController::class, 'find'])->name('tenant.subscription.find');
            });

            Route::prefix('patients')->group(function () {
                Route::get('/', [PatientController::class, 'index'])->name('tenant.patient.index');
                Route::post('/list', [PatientController::class, 'list'])->name('tenant.patient.list');
                Route::get('/create', [PatientController::class, 'create'])->name('tenant.patient.create');
                Route::post('/store', [PatientController::class, 'store'])->name('tenant.patient.store');
                Route::post('/disable', [PatientController::class, 'disable'])->name('tenant.patient.disable');
                Route::get('/edit/{id}', [PatientController::class, 'edit'])->name('tenant.patient.edit');
                Route::get('/get-patient-by-id/{id}', [PatientController::class, 'getPatientById'])->name('tenant.patient.getPatientById');
                Route::put('/update/{id}', [PatientController::class, 'update'])->name('tenant.patient.update');

                Route::get('/{patient_id}/anamnese', [AnamneseController::class, 'index'])->name('tenant.anamnese.index');
                Route::post('/{patient_id}/anamnese/store', [AnamneseController::class, 'store'])->name('tenant.anamnese.store');
                Route::get('/{patient_id}/anamnese/find', [AnamneseController::class, 'find'])->name('tenant.anamnese.find');

                Route::get('/anamnese-pdf/{patient_id}', [PatientController::class, 'pdf'])->name('tenant.medicalRecord.pdf');
                Route::get('/prontuario-pdf/{patient_id}', [PatientController::class, 'pdfProntuario'])->name('tenant.medicalRecord.pdf.prontuario');

                Route::post('/upload', [PatientController::class, 'upload'])->name('tenant.upload');
            });

            Route::prefix('/configuration')->group(function () {
                Route::get('/', [ConfigurationController::class, 'index'])->name('tenant.configuration.index');
                Route::get('/get-config', [ConfigurationController::class, 'getConfig'])->name('getConfig');
                Route::post('/confirm-first-access', [ConfigurationController::class, 'confirmFirstAccess'])->name('tenant.configuration.confirmFirstAccess');
                Route::get('/schedule/list', [ConfigurationController::class, 'listSchedule'])->name('tenant.configuration.listSchedule');
            });

            Route::prefix('/appointments')->group(function () {
                Route::get('/', [AppointmentController::class, 'index'])->name('tenant.appointment.index');
                Route::get('/list', [AppointmentController::class, 'list'])->name('tenant.appointment.list');
                Route::get('/get-callendar', [AppointmentController::class, 'getCallendar'])->name('tenant.appointment.getCallendar');
                Route::get('/get-patients', [AppointmentController::class, 'getPatients'])->name('tenant.appointment.getPatients');
                Route::post('/create', [AppointmentController::class, 'create'])->name('tenant.appointment.create');
                Route::post('/remove-patient', [AppointmentController::class, 'removePatient'])->name('tenant.appointment.removePatient');
                Route::get('/find-for-consultation/{idPatient}/{day}/{hour}', [AppointmentController::class, 'findForConsultation'])->name('tenant.appointment.findForConsultation');
            });

            Route::prefix('/consultation')->group(function () {
                Route::get('/', [ConsultationController::class, 'index'])->name('tenant.consultation.index');
                Route::post('/list', [ConsultationController::class, 'list'])->name('tenant.consultation.list');
                Route::post('/store', [ConsultationController::class, 'store'])->name('tenant.consultation.store');
                Route::get('/show/{id}', [ConsultationController::class, 'show'])->name('tenant.consultation.show');
                Route::get('/find/{id}', [ConsultationController::class, 'find'])->name('tenant.consultation.find');
                Route::put('/update/{id}', [ConsultationController::class, 'update'])->name('tenant.consultation.update');
            });

            // Route::prefix('/employees')->middleware('check.plan')->group(function () {
            //     Route::get('/', [EmployeeController::class, 'index'])->name('tenant.employee.index');
            //     Route::post('/list', [EmployeeController::class, 'list'])->name('tenant.employee.list');
            //     Route::get('/create', [EmployeeController::class, 'create'])->name('tenant.employee.create');
            //     Route::post('/store', [EmployeeController::class, 'store'])->name('tenant.employee.store');
            //     Route::post('/disable/{id}', [EmployeeController::class, 'disable'])->name('tenant.employee.disable');
            //     Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('tenant.employee.edit');
            //     Route::get('/find/{id}', [EmployeeController::class, 'find'])->name('tenant.employee.find');
            //     Route::put('/update/{id}', [EmployeeController::class, 'update'])->name('tenant.employee.update');
            // });

            Route::get('/reports', [TenantController::class, 'reports'])->name('tenant.reports');
        });
    });
});

// Route::get('/temp-pagseguro-key', function () {
//     $response = \Illuminate\Support\Facades\Http::withoutVerifying()
//         ->withToken(config('pagseguro.token'))
//         ->post(config('pagseguro.base_url') . '/public-keys', [
//             'type' => 'card'
//         ]);

//     dd($response->json());
// });

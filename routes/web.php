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
Route::get('/cadastre-se', [SiteController::class, 'formFacaCadastro'])->name('form.cadastro');
Route::post('/cadastre-se', [SiteController::class, 'cadastro'])->name('cadastro');

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
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.logs.index');
        Route::get('/audit-logs/export-csv', [AuditLogController::class, 'exportCsv'])->name('audit.logs.exportCsv');

        Route::prefix('financial/')->group(function () {

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

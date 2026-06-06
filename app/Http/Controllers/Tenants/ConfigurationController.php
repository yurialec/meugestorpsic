<?php

namespace App\Http\Controllers\Tenants;

use App\Http\Controllers\Controller;
use App\Services\Tenants\ConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ConfigurationController extends Controller
{
    protected $ConfigurationService;

    public function __construct(ConfigurationService $ConfigurationService)
    {
        $this->ConfigurationService = $ConfigurationService;
    }

    public function index()
    {
        return view("tenant.configuration.index");
    }

    public function confirmFirstAccess()
    {
        $confirmedMessage = $this->ConfigurationService->confirmFirstAccess();
        if ($confirmedMessage) {
            return response()->json([
                'status' => true,
                'confirmedMessage' => $confirmedMessage,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao localizar configurações'
            ], 500);
        }
    }

    public function storeConsultationDuration(Request $request)
    {
        $duration = $this->ConfigurationService->storeConsultationDuration($request->all());

        if ($duration) {
            return response()->json([
                'status' => true,
                'duration' => $duration,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar tempo de atendimento'
            ], 500);
        }
    }

    public function storeConsultationPrice(Request $request)
    {
        $duration = $this->ConfigurationService->storeConsultationPrice($request->all());

        if ($duration) {
            return response()->json([
                'status' => true,
                'duration' => $duration,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar preço de atendimento'
            ], 500);
        }
    }

    public function availablePlans()
    {
        $plans = $this->ConfigurationService->availablePlans();
        if ($plans) {
            return response()->json([
                'status' => true,
                'plans' => $plans,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao exibir planos'
            ], 500);
        }
    }

    public function getConfig()
    {
        $config = $this->ConfigurationService->getConfig();

        if ($config) {
            return response()->json([
                'status' => true,
                'config' => $config,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao exibir configurações.'
            ], 500);
        }
    }

    public function logoStore(Request $request)
    {
        $postData = $request->only('file');
        $file = $postData['file'];
        $fileArray = ['image' => $file];

        $rules = array(
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000'
        );

        $validator = Validator::make($fileArray, $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->getMessages(),
            ], 400);
        }

        $logo = $this->ConfigurationService->logoStore($fileArray);

        if ($logo) {
            return response()->json([
                'status' => true,
                'logo' => $logo,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao cadastrar logo.'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $profileTable = session('is_admin') ? 'clients' : 'employees';
        $profileId = session('is_admin')
            ? auth()->guard('client')->id()
            : auth()->guard('employee')->id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique($profileTable, 'email')->ignore($profileId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'consultation_duration' => ['nullable', 'integer', 'min:1', 'max:999'],
            'consultation_price' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'json'],
            'schedule' => ['nullable', 'json'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        $validated['address'] = json_decode($request->input('address', '{}'), true) ?: [];
        $validated['schedule'] = json_decode($request->input('schedule', '{}'), true) ?: [];

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo');
        }

        $profile = $this->ConfigurationService->updateProfileConfiguration($validated);

        if ($profile) {
            return response()->json([
                'status' => true,
                'profile' => $profile,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Erro ao salvar configurações.',
        ], 500);
    }

    public function listSchedule()
    {
        $schedule = $this->ConfigurationService->listSchedule();

        if ($schedule) {
            return response()->json([
                'status' => true,
                'schedule' => $schedule,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao exibir schedule.'
            ], 500);
        }
    }
}

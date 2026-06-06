<?php

namespace App\Services\Tenants;

use App\Models\Tenants\Tenant;
use App\Repositories\Tenants\FinanceRepository;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Log;

class FinanceService
{
    protected $FinanceRepository;

    public function __construct(FinanceRepository $FinanceRepository)
    {
        $this->FinanceRepository = $FinanceRepository;
    }

    public function all($data)
    {
        $dataSearch = [
            'year' => isset($data['year']) ? $data['year'] : null,
            'month' => isset($data['month']) ? $data['month'] : null,
        ];

        return $this->FinanceRepository->all($dataSearch);
    }

    public function getPdf($data)
    {        
        try {

            $dataSearch = [
                'year' => isset($data['year']) ? $data['year'] : null,
                'month' => isset($data['month']) ? $data['month'] : null,
            ];

            $client = Auth::guard('client')->user()->toArray();
            $finance = $this->FinanceRepository->getPdf($dataSearch)->toArray();

            $psychologistLogo = null;
            $tenant = Tenant::find(session('tenant_id'));
            if ($tenant && $tenant->logo) {
                $imagePath = storage_path('app/public/' . $tenant->logo);
                if (file_exists($imagePath) && is_readable($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $psychologistLogo = 'data:image/' . $imageType . ';base64,' . $imageData;
                }
            }

            $pdf = Pdf::loadView('tenant.pdfs.finance.finance', compact('finance', 'client', 'psychologistLogo'));

            return $pdf;
        } catch (Exception $err) {
            Log::error('Erro ao gerar pdf', [$err->getMessage()]);
            return false;
        }
    }

    public function billsToReciveList($dataSearch)
    {
        return $this->FinanceRepository->billsToReciveList($dataSearch);
    }

    public function registerPayment($id, $data)
    {
        return $this->FinanceRepository->registerPayment($id, $data);
    }
}
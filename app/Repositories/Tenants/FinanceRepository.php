<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\FinanceRepositoryInterface;
use App\Models\Tenants\Patient;
use App\Models\Tenants\TenantFinancialInstallment;
use App\Models\Tenants\TenantFinancialTransaction;
use Carbon\Carbon;
use DB;
use Exception;
use Log;

class FinanceRepository implements FinanceRepositoryInterface
{
    protected $financeTransaction;
    protected $patient;
    protected $financialInstallment;


    public function __construct(
        TenantFinancialTransaction $financeTransaction,
        Patient $patient,
        TenantFinancialInstallment $financialInstallment
    ) {
        $this->financeTransaction = $financeTransaction;
        $this->patient = $patient;
        $this->financialInstallment = $financialInstallment;
    }

    public function all($dataSearch)
    {
        try {

            $year = $dataSearch['year'];
            $month = $dataSearch['month'];

            if ($month !== null) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            } else {
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = Carbon::create($year, 12, 31)->endOfYear();
            }

            $baseQuery = $this->financeTransaction
                ->whereBetween('created_at', [$startDate, $endDate]);

            $totalRecipe = (clone $baseQuery)->sum('amount');

            $totalTransactions = (clone $baseQuery)->count();
            $averageTicket = $totalTransactions > 0
                ? round($totalRecipe / $totalTransactions, 2)
                : 0.00;

            $yearsOfActivity = $this->financeTransaction
                ->where('status', TenantFinancialTransaction::STATUS_PAID)
                ->select(DB::raw('YEAR(paid_at) as year'))
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->pluck('year');

            $finance = (clone $baseQuery)
                ->with(['patient:id,full_name', 'paymentMethod:id,name,label'])
                ->latest('paid_at')
                ->paginate(5);

            $patientQuery = $this->patient
                ->newQuery()
                ->whereBetween('created_at', [$startDate, $endDate]);

            $patients = [
                'total' => $patientQuery->count(),
                'newest' => 654,
            ];

            $results = (clone $baseQuery)
                ->select([
                    'status',
                    DB::raw('sum(amount) as amount'),
                    DB::raw('count(*) as quantity')
                ])
                ->groupBy('status')
                ->get();

            $donut = $results->toarray();

            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $months[] = Carbon::now()
                    ->startOfMonth()
                    ->subMonths($i)
                    ->format('Y-m');
            }

            $rawData = $baseQuery->selectRaw("
                    DATE_FORMAT(paid_at, '%Y-%m') as month,
                    SUM(amount) as total_amount,
                    COUNT(*) as payment_count
                ")
                ->where('status', TenantFinancialTransaction::STATUS_PAID)
                ->where('paid_at', '>=', Carbon::now()->startOfMonth()->subMonths(5))
                ->where('paid_at', '<', Carbon::now()->addMonth()->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $bars = collect($months)->map(function ($month) use ($rawData) {
                $data = $rawData->get($month);

                return (object) [
                    'month' => $month,
                    'label' => Carbon::createFromFormat('Y-m', $month)
                        ->locale('pt_BR')
                        ->translatedFormat("M"),
                    'total_amount' => $data ? (float) $data->total_amount : 0.0,
                    'payment_count' => $data ? (int) $data->payment_count : 0,
                ];
            });

            $current = Carbon::now();
            $current->locale('pt_BR');
            $currentMonthYear = $current->translatedFormat('F Y');

            return [
                'totalRecipe' => (float) $totalRecipe,
                'patients' => $patients,
                'averageTicket' => $averageTicket,
                'currentMonthYear' => $currentMonthYear,
                'finance' => $finance,
                'yearsOfActivity' => $yearsOfActivity,
                'totalTransactions' => $totalTransactions,
                'chart' => [
                    'donut' => $donut,
                    'bars' => $bars,
                ],
            ];

        } catch (\Throwable $err) {
            Log::error('Erro crítico em FinanceService::all', [
                'message' => $err->getMessage(),
                'file' => $err->getFile(),
                'line' => $err->getLine(),
                'trace' => $err->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Falha ao carregar os dados financeiros.',
                'debug' => config('app.debug') ? $err->getMessage() : null,
            ];
        }
    }

    public function getPdf($dataSearch)
    {
        $month = $dataSearch['month'] ?? null;
        $year = $dataSearch['year'] ?? date('Y');

        try {
            if ($month !== null) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            } else {
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = Carbon::create($year, 12, 31)->endOfYear();
            }

            $transactions = $this->financeTransaction
                ->with(['patient', 'paymentMethod'])
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->orderBy('paid_at', 'asc')
                ->get();

            return $transactions;
        } catch (Exception $err) {
            Log::error('Erro ao gerar PDF de transações financeiras', [
                'message' => $err->getMessage(),
                'trace' => $err->getTraceAsString(),
                'year' => $year,
                'month' => $month,
            ]);

            throw $err;
        }
    }

    public function create(array $data)
    {
        return $this->financeTransaction::create($data);
    }

    public function billsToReciveList($dataSearch)
    {
        $filter = $dataSearch['filter'] ?? [];

        try {
            $query = $this->financeTransaction
                ->where('status', '<>', TenantFinancialTransaction::STATUS_FREE)
                ->with([
                    'patient:id,full_name',
                    'paymentMethod:id,name,label',
                    'installments'
                ]);

            $year = $filter['year'] ?? null;
            $month = $filter['month'] ?? null;

            if ($year) {
                $query->whereYear('paid_at', $year);
                if ($month) {
                    $query->whereMonth('paid_at', $month);
                }
            }

            if (!empty($filter['patient'])) {
                $query->where('patient_id', $filter['patient']);
            }

            if (!empty($filter['paymentMethod'])) {
                $query->where('payment_method_id', $filter['paymentMethod']);
            }

            if (!empty($filter['selectedStatus'])) {
                $query->where('status', $filter['selectedStatus']);
            }

            $result = $query->paginate(10);

            return $result;

        } catch (Exception $err) {
            Log::error('Erro ao listar contas a pagar', [
                'message' => $err->getMessage(),
                'filter' => $filter,
            ]);
            throw $err;
        }
    }

    public function registerPayment($id, $data)
    {
        try {
            DB::beginTransaction();

            $bill = $this->financeTransaction->findOrFail($id);

            $originalAmount = $bill->amount + $bill->discount; // valor bruto
            $newDiscount = isset($data['discount']) && is_numeric($data['discount'])
                ? max(0, (float) $data['discount'])
                : $bill->discount;

            if ($newDiscount > $originalAmount) {
                throw new \InvalidArgumentException('O desconto não pode ser maior que o valor bruto.');
            }

            $newAmount = $originalAmount - $newDiscount;

            $bill->discount = $newDiscount;
            $bill->amount = $newAmount;
            $bill->status = TenantFinancialTransaction::STATUS_PAID;
            $bill->paid_at = Carbon::now();
            $bill->payment_method_id = $data['payment_method_id'];
            $bill->save();

            if (
                $bill->payment_method_id == 3 &&
                isset($data['credit_type']) &&
                $data['credit_type'] === 'parcelado' &&
                isset($data['installments'])
            ) {
                $installmentsCount = (int) $data['installments'];

                if ($installmentsCount < 1 || $installmentsCount > 12) {
                    throw new \InvalidArgumentException('Número de parcelas inválido.');
                }

                $this->financialInstallment
                    ->where('financial_transaction_id', $bill->id)
                    ->delete();

                $baseAmount = round($bill->amount / $installmentsCount, 2);
                $totalBase = $baseAmount * ($installmentsCount - 1);
                $lastAmount = round($bill->amount - $totalBase, 2);

                for ($i = 1; $i <= $installmentsCount; $i++) {
                    $this->financialInstallment->create([
                        'financial_transaction_id' => $bill->id,
                        'num' => $i,
                        'amount' => ($i === $installmentsCount) ? $lastAmount : $baseAmount,
                    ]);
                }
            } else {
                $this->financialInstallment
                    ->where('financial_transaction_id', $bill->id)
                    ->delete();
            }

            DB::commit();
            return $bill;
        } catch (Exception $err) {
            DB::rollBack();
            Log::error('Erro ao registrar pagamento', [
                'id' => $id,
                'data' => $data,
                'error' => $err->getMessage(),
                'trace' => $err->getTraceAsString(),
            ]);
            throw $err;
        }
    }
}
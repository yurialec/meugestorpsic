<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use BackedEnum;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()->latest('occurred_at');

        if ($request->filled(['start_date', 'end_date'])) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        if ($request->filled('user')) {
            $query->byUser($request->user);
        }

        if ($request->filled('event_type')) {
            $query->byEventType($request->event_type);
        }

        return AuditLogResource::collection(
            $query->paginate((int) $request->input('per_page', 25))
        );
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = AuditLog::query()->latest('occurred_at');

        if ($request->filled(['start_date', 'end_date'])) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        if ($request->filled('user')) {
            $query->byUser($request->user);
        }

        if ($request->filled('event_type')) {
            $query->byEventType($request->event_type);
        }

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['id', 'ip_address', 'user_email', 'event_type', 'timestamp', 'metadata']);

            $query->chunk(500, function ($logs) use ($output) {
                foreach ($logs as $log) {
                    fputcsv($output, [
                        $log->id,
                        $log->ip_address,
                        $log->user_email,
                        $log->event_type instanceof BackedEnum ? $log->event_type->value : $log->event_type,
                        optional($log->occurred_at)->timezone(config('audit.timezone'))->toIso8601String(),
                        json_encode($log->metadata, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });

            fclose($output);
        }, 'audit_logs.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

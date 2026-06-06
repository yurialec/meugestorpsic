<?php

namespace App\Contracts\Audit;

use App\Enums\AuditEventType;
use Illuminate\Http\Request;

interface AuditLogger
{
    public function log(AuditEventType|string $eventType, array $data = [], ?Request $request = null): void;
}

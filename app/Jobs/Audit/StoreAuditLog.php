<?php

namespace App\Jobs\Audit;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreAuditLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly array $payload)
    {
        $this->onConnection(config('audit.queue.connection'));
        $this->onQueue(config('audit.queue.queue'));
    }

    public function handle(): void
    {
        AuditLog::create($this->payload);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PruneAuditLogs extends Command
{
    protected $signature = 'audit:prune {--dry-run : Show how many records would be pruned}';

    protected $description = 'Prune audit logs according to config/audit.php retention policy';

    public function handle(): int
    {
        $retentionDays = (int) config('audit.retention_days');

        if ($retentionDays <= 0) {
            $this->warn('Audit retention is disabled because retention_days is zero or negative.');

            return self::SUCCESS;
        }

        $cutoff = Carbon::now(config('audit.timezone'))->subDays($retentionDays);
        $query = DB::table('audit_logs')->where('occurred_at', '<', $cutoff);
        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Audit logs eligible for pruning: {$count}");

            return self::SUCCESS;
        }

        $query->delete();
        $this->info("Audit logs pruned: {$count}");

        return self::SUCCESS;
    }
}

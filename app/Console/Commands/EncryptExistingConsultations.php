<?php

namespace App\Console\Commands;

use App\Casts\EncryptedSessionCast;
use App\Models\Tenants\Consultation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptExistingConsultations extends Command
{
    protected $signature = 'consultations:encrypt-existing {--dry-run : Count records without writing changes}';

    protected $description = 'Encrypt plaintext sensitive consultation fields in batches';

    public function handle(): int
    {
        $cast = new EncryptedSessionCast();
        $encryptedRows = 0;

        DB::table('consultations')
            ->orderBy('id')
            ->chunkById(100, function ($consultations) use ($cast, &$encryptedRows) {
                foreach ($consultations as $row) {
                    $attributes = (array) $row;

                    $model = new Consultation();
                    $model->exists = true;
                    $model->setRawAttributes($attributes, true);

                    $updates = [];

                    foreach (Consultation::sensitiveEncryptedFields() as $field) {
                        $value = $attributes[$field] ?? null;

                        if ($value === null || $value === '' || str_starts_with((string) $value, 'enc_session:v2:')) {
                            continue;
                        }

                        $updates[$field] = $cast->set($model, $field, $value, $attributes);
                    }

                    if ($updates === []) {
                        continue;
                    }

                    $encryptedRows++;

                    if (!$this->option('dry-run')) {
                        DB::table('consultations')->where('id', $attributes['id'])->update($updates);
                    }
                }
            });

        $this->info("Consultations with plaintext fields found: {$encryptedRows}");

        return self::SUCCESS;
    }
}

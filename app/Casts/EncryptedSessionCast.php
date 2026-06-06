<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Encrypts consultation clinical fields at rest using the application key.
 */
class EncryptedSessionCast implements CastsAttributes
{
    private const PREFIX = 'enc_session:v2:';

    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!$this->isEncrypted($value)) {
            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, strlen(self::PREFIX)));
        } catch (DecryptException $exception) {
            Log::warning('consultation_decrypt_failed', [
                'consultation_id' => $model->getKey(),
                'field' => $key,
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value) && $this->isEncrypted($value)) {
            return $value;
        }

        return self::PREFIX . Crypt::encryptString((string) $value);
    }

    private function isEncrypted(string $value): bool
    {
        return Str::startsWith($value, self::PREFIX);
    }
}

<?php

namespace App\Helpers;

class Utils
{
    /**
     * Remove todos os caracteres não numéricos de um valor.
     * @param mixed $value
     * @return int
     */
    public static function sanitizeInteger($value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    /**
     * Limpar moeda
     * @param mixed $value
     * @return float
     */
    public static function sanitizeCurrency($value): float
    {
        $value = trim($value);
        $value = preg_replace('/[^\d,.-]/', '', $value);

        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }

    /**
     * boolean para inteiro
     * @param mixed $value
     * @return int
     */
    public static function booleanToInt($value): int
    {
        if (is_string($value)) {
            $value = strtolower($value);
            return in_array($value, ['1', 'true', 'yes', 'on']) ? 1 : 0;
        }

        return $value ? 1 : 0;
    }

    /**
     * Limpar dominio
     * @param string $value
     * @return string
     */
    public static function sanitizeDomain(string $value): string
    {
        $value = preg_replace('/[^a-zA-Z\s-]/', '', $value);
        $value = preg_replace('/\d+/', '', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        $value = str_replace(' ', '_', $value);

        $value = preg_replace('/_+/', '_', $value);

        $value = trim($value, '_');

        $value = strtolower($value);

        return $value;
    }

    /**
     * setar limite de usuarios
     * @param string $type
     * @param string $userLimit
     * @return int
     */
    public static function userLimit(string $type, string $userLimit): int
    {
        $limits = [
            'basico' => 5,
            'pro' => 10,
            'premium' => 20,
        ];

        if ($type === 'individual') {
            return 1;
        }

        return $limits[$userLimit];
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class PortalCredentialService
{
    public function encryptString(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        return Crypt::encryptString($value);
    }

    public function decryptString(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        return Crypt::decryptString($value);
    }

    public function encryptJson(?array $value): ?string
    {
        if (!$value) return null;
        return Crypt::encryptString(json_encode($value, JSON_UNESCAPED_UNICODE));
    }

    public function decryptJson(?string $value): ?array
    {
        if ($value === null || $value === '') return null;
        $raw = Crypt::decryptString($value);
        $arr = json_decode($raw, true);
        return is_array($arr) ? $arr : null;
    }
}

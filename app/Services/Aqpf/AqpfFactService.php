<?php

namespace App\Services\Aqpf;

use Illuminate\Support\Facades\Http;

class AqpfFactService
{
    public static function baseUrl(): string
    {
        return rtrim((string) config('services.aqpf.base_url'), '/');
    }

    public static function token(): string
    {
        return (string) config('services.aqpf.token');
    }

    public static function ruc(string $ruc): array
    {
        $ruc = preg_replace('/\D+/', '', $ruc);

        $res = Http::acceptJson()
            ->timeout(20)
            ->withToken(self::token()) // Authorization: Bearer <token>
            ->get(self::baseUrl().'/ruc/'.$ruc);

        return [
            'ok' => $res->successful(),
            'status' => $res->status(),
            'json' => $res->json(),
            'raw' => $res->body(),
        ];
    }

    public static function dni(string $dni): array
    {
        $dni = preg_replace('/\D+/', '', $dni);

        $res = Http::acceptJson()
            ->timeout(20)
            ->withToken(self::token())
            ->get(self::baseUrl().'/dni/'.$dni);

        return [
            'ok' => $res->successful(),
            'status' => $res->status(),
            'json' => $res->json(),
            'raw' => $res->body(),
        ];
    }
}

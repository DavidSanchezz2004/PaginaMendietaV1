<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RobotClient
{
    public function post(string $path, array $payload = [])
    {
        $base = rtrim((string) config('services.robot.base_url'), '/');
        $url  = $base . '/' . ltrim($path, '/');

        $headers = [
            'x-api-key' => (string) config('services.robot.api_key'),
        ];

        // Cloudflare Access (si lo tienes activado)
        $cfId = (string) config('services.robot.cf_client_id');
        $cfSecret = (string) config('services.robot.cf_client_secret');
        if ($cfId !== '' && $cfSecret !== '') {
            $headers['CF-Access-Client-Id'] = $cfId;
            $headers['CF-Access-Client-Secret'] = $cfSecret;
        }

        $timeout = (int) config('services.robot.timeout', 60);

        return Http::withHeaders($headers)
            ->timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);
    }
}

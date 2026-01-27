<?php

namespace App\Services\Feasy;

use Illuminate\Support\Facades\Http;

class FeasyClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
        private readonly string $authMode = 'bearer',
        private readonly int $timeout = 30,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: rtrim((string) config('services.feasy.base_url'), '/'),
            token: (string) config('services.feasy.token'),
            authMode: (string) config('services.feasy.auth_mode', 'bearer'),
            timeout: (int) config('services.feasy.timeout', 30),
        );
    }

    private function headers(): array
    {
        return match (strtolower($this->authMode)) {
            'token' => ['Token' => $this->token, 'Accept' => 'application/json'],
            default => ['Authorization' => 'Bearer '.$this->token, 'Accept' => 'application/json'],
        };
    }

    public function enviarFactura(array $payload): array
    {
        $url = $this->baseUrl.'/comprobante/enviar_factura';

        $response = Http::withHeaders($this->headers())
            ->timeout($this->timeout)
            ->asJson()
            ->post($url, $payload);

        return [
            'ok' => $response->successful(),
            'http_status' => $response->status(),
            'json' => $response->json(),
            'raw' => $response->body(),
        ];
    }
}

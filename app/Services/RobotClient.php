<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RobotClient
{
    protected ?string $baseUrl = null;

    /**
     * Permite especificar una URL base específica (para worker pool).
     */
    public function setBaseUrl(?string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function post(string $path, array $payload = [], array $metaHeaders = [])
    {
        $base = $this->baseUrl 
            ? rtrim($this->baseUrl, '/') 
            : rtrim((string) config('services.robot.base_url'), '/');
            
        $url  = $base . '/' . ltrim($path, '/');

        $headers = [
            'Accept'    => 'application/json',
            'x-api-key' => (string) config('services.robot.api_key'),
        ];

        // ✅ Multi-tenant headers (si vienen)
        foreach (['x-company-id','x-portal','x-device-id'] as $h) {
            if (!empty($metaHeaders[$h])) {
                $headers[$h] = (string) $metaHeaders[$h];
            }
        }

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

    /**
     * Método GET para endpoints del robot (buzon/list, files/{token}, etc.)
     */
    public function get(string $path, array $queryParams = [], array $metaHeaders = [])
    {
        $base = $this->baseUrl 
            ? rtrim($this->baseUrl, '/') 
            : rtrim((string) config('services.robot.base_url'), '/');
            
        $url  = $base . '/' . ltrim($path, '/');

        $headers = [
            'Accept'    => 'application/json',
            'x-api-key' => (string) config('services.robot.api_key'),
        ];

        // ✅ Multi-tenant headers
        foreach (['x-company-id','x-portal','x-device-id'] as $h) {
            if (!empty($metaHeaders[$h])) {
                $headers[$h] = (string) $metaHeaders[$h];
            }
        }

        // Cloudflare Access
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
            ->get($url, $queryParams);
    }

    /**
     * Método DELETE para cerrar sesiones u otros recursos
     * DELETE /sunat/close/{session_id}
     */
    public function delete(string $path, array $metaHeaders = [])
    {
        $base = $this->baseUrl 
            ? rtrim($this->baseUrl, '/') 
            : rtrim((string) config('services.robot.base_url'), '/');
            
        $url  = $base . '/' . ltrim($path, '/');

        $headers = [
            'Accept'    => 'application/json',
            'x-api-key' => (string) config('services.robot.api_key'),
        ];

        // ✅ Multi-tenant headers
        foreach (['x-company-id','x-portal','x-device-id'] as $h) {
            if (!empty($metaHeaders[$h])) {
                $headers[$h] = (string) $metaHeaders[$h];
            }
        }

        // Cloudflare Access
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
            ->delete($url);
    }
}

<?php

namespace App\Services\Feasy;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class FeasyInvoiceService
{
    protected string $baseUrl;
    protected string $token;
    protected string $authMode;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.feasy.base_url', 'https://api.feasyperu.com/api'),
            '/'
        );

        $this->token = (string) config('services.feasy.token');
        $this->authMode = (string) config('services.feasy.auth_mode', 'bearer'); // bearer|token
        $this->timeout = (int) config('services.feasy.timeout', 30);
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * Cliente HTTP con auth FEASY
     */
    protected function request(): PendingRequest
    {
        $req = Http::timeout($this->timeout)->acceptJson();

        if ($this->authMode === 'bearer') {
            return $req->withToken($this->token);
        }

        return $req->withHeaders([
            'Authorization' => $this->token,
        ]);
    }

    /**
     * Normaliza la respuesta FEASY
     * FEASY puede devolver HTTP 200 con success=false
     */
    protected function wrap(Response $response): array
    {
        $json = $response->json();
        $jsonSuccess = is_array($json)
            ? (data_get($json, 'success') === true)
            : false;

        return [
            'success' => $jsonSuccess,
            'status'  => $response->status(),
            'json'    => $json,
            'raw'     => $response->body(),
        ];
    }

    /**
     * Emitir Factura (01)
     */
    public function emitirFacturaGravada(array $payload): array
    {
        $url = $this->baseUrl . '/comprobante/enviar_factura';
        $response = $this->request()->post($url, $payload);
        return $this->wrap($response);
    }

    /**
     * Consultar Comprobante (Factura)
     */
    public function consultarComprobante(array $payload): array
    {
        $url = $this->baseUrl . '/comprobante/consultar';
        $response = $this->request()->post($url, $payload);
        return $this->wrap($response);
    }
}

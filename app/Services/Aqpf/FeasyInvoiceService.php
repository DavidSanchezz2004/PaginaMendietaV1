<?php

namespace App\Services\Feasy;

use RuntimeException;

class FeasyInvoiceService
{
    public function __construct(private readonly FeasyClient $client) {}

    public static function make(): self
    {
        return new self(FeasyClient::fromConfig());
    }

    public function emitirFacturaGravada(array $payload): array
    {
        if (!config('services.feasy.token')) {
            // Si aún no tienes token Feasy, te devolvemos el payload (para que puedas avanzar).
            return [
                'success' => false,
                'status' => 'NO_TOKEN',
                'message' => 'FEASY_TOKEN no configurado. Payload listo para enviar.',
                'payload' => $payload,
            ];
        }

        $res = $this->client->enviarFactura($payload);

        if (!$res['ok']) {
            return [
                'success' => false,
                'status' => 'ERROR_HTTP',
                'http_status' => $res['http_status'],
                'message' => 'Error llamando a Feasy',
                'raw' => $res['json'] ?? $res['raw'],
            ];
        }

        // Como no tenemos el schema de respuesta exacto, devolvemos “raw”
        return [
            'success' => true,
            'status' => 'OK',
            'raw' => $res['json'],
        ];
    }
}

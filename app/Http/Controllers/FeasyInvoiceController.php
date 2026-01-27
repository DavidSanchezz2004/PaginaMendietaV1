<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Aqpf\AqpfFactService;
use App\Services\Feasy\FeasyInvoiceService;
use App\Models\FeasyInvoice;
use Illuminate\Support\Facades\Log;


class FeasyInvoiceController extends Controller
{
    public function create()
    {
        return view('equipo.facturas.create');
    }

    public function lookupRuc(string $ruc)
    {
        if (!config('services.aqpf.token')) {
            return response()->json(['ok' => false, 'message' => 'AQPF_API_TOKEN no configurado'], 422);
        }
        return response()->json(AqpfFactService::ruc($ruc));
    }

    public function lookupDni(string $dni)
    {
        if (!config('services.aqpf.token')) {
            return response()->json(['ok' => false, 'message' => 'AQPF_API_TOKEN no configurado'], 422);
        }
        return response()->json(AqpfFactService::dni($dni));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'serie' => ['required','string','max:4'],
            'numero' => ['required','string','max:8'],
            'fecha_emision' => ['required','date'],
            'hora_emision' => ['required','date_format:H:i'],

            // FACTURA (01): solo RUC
            'cliente_tipo_doc'   => ['required','string','in:6'],
            'cliente_numero_doc' => ['required','string','regex:/^\d{11}$/'],
            'cliente_nombre'     => ['required','string','max:200'],
            'cliente_correo'     => ['nullable','email','max:200'],
            'cliente_direccion'  => ['nullable','string','max:200'],

            'items' => ['required','array','min:1'],
            'items.*.tipo' => ['required','in:P,S'],
            'items.*.codigo' => ['required','string','max:30'],
            'items.*.unidad' => ['required','string','max:5'],
            'items.*.descripcion' => ['required','string','max:300'],
            'items.*.cantidad' => ['required','numeric','min:0.01'],
            'items.*.precio_unitario' => ['required','numeric','min:0.01'], // con IGV
        ]);

        $payload = $this->buildFeasyPayload($data);

        // DEBUG opcional:
        // return response()->json(['payload' => $payload], 200);

       $response = FeasyInvoiceService::make()->emitirFacturaGravada($payload);

// ⬇️⬇️⬇️ AQUÍ SE GUARDA ⬇️⬇️⬇️
try {
    $serie = strtoupper($data['serie']);
    $numero = str_pad($data['numero'], 8, '0', STR_PAD_LEFT);

    $json = $response['json'] ?? [];

    FeasyInvoice::updateOrCreate(
        [
            'codigo_tipo_documento' => '01',
            'serie' => $serie,
            'numero' => $numero,
        ],
        [
            // Cliente
            'cliente_tipo_doc'   => $data['cliente_tipo_doc'],
            'cliente_numero_doc' => $data['cliente_numero_doc'],
            'cliente_nombre'     => $data['cliente_nombre'],
            'cliente_correo'     => $data['cliente_correo'] ?? null,
            'cliente_direccion'  => $data['cliente_direccion'] ?? null,

            // Totales
            'monto_gravado' => data_get($payload, 'informacion_documento.monto_total_gravado', 0),
            'monto_igv'     => data_get($payload, 'informacion_documento.monto_total_igv', 0),
            'monto_total'   => data_get($payload, 'informacion_documento.monto_total', 0),

            // Estado FEASY / SUNAT
            'success'           => (bool) data_get($json, 'success', false),
            'codigo_respuesta'  => data_get($json, 'data.codigo_respuesta'),
            'mensaje_respuesta' => data_get($json, 'data.mensaje_respuesta'),
            'nombre_archivo_xml'=> data_get($json, 'data.nombre_archivo_xml'),

            // Auditoría
            'payload'  => $payload,
            'response' => $response,
        ]
    );
} catch (\Throwable $e) {
    // MUY IMPORTANTE: no rompas la emisión si falla la BD
    Log::error('Error guardando feasy_invoices', [
        'error' => $e->getMessage(),
    ]);
}
// ⬆️⬆️⬆️ FIN GUARDADO ⬆️⬆️⬆️

return response()->json(
    $response,
    ($response['success'] ?? false) === true ? 200 : 400
);

    }

    private function buildFeasyPayload(array $data): array
    {
        $igvRate = 0.18;

        $serie = strtoupper($data['serie']);
        $numero = str_pad($data['numero'], 8, '0', STR_PAD_LEFT);
        $codigoInterno = '01'.$serie.$numero;

        $emisor = [
            'codigo_tipo_documento_emisor' => '6',
            'numero_documento_emisor' => '10724474948',
            'nombre_razon_social_emisor' => 'SANCHEZ QUICAÑO DAVID AARON',
            'ubigeo_emisor' => '150101',
            'departamento_emisor' => 'LIMA',
            'provincia_emisor' => 'LIMA',
            'distrito_emisor' => 'LIMA',
            'urbanizacion_emisor' => null,
            'direccion_emisor' => 'Av. Lima 123',
        ];

        $adquiriente = [
            'codigo_tipo_documento_adquiriente' => $data['cliente_tipo_doc'],
            'numero_documento_adquiriente' => $data['cliente_numero_doc'],
            'nombre_razon_social_adquiriente' => $data['cliente_nombre'],
            'codigo_pais_adquiriente' => 'PE',
            'ubigeo_adquiriente' => '150101',
            'departamento_adquiriente' => 'LIMA',
            'provincia_adquiriente' => 'LIMA',
            'distrito_adquiriente' => 'LIMA',
            'urbanizacion_adquiriente' => null,
            'direccion_adquiriente' => $data['cliente_direccion'] ?: '—',
            'correo_adquiriente' => $data['cliente_correo'] ?? null,
        ];

        $items = [];
        $sumGravado = 0.0;
        $sumIgv = 0.0;
        $sumTotal = 0.0;

        foreach ($data['items'] as $idx => $it) {
            $cantidad = (float) $it['cantidad'];
            $precioUnit = (float) $it['precio_unitario']; // con IGV
            $total = $cantidad * $precioUnit;

            $valorUnit = $precioUnit / (1 + $igvRate);
            $valorTotal = $cantidad * $valorUnit;
            $igv = $total - $valorTotal;

            $valorUnitR = round($valorUnit, 10);
            $valorTotalR = round($valorTotal, 4);
            $igvR = round($igv, 2);
            $totalR = round($total, 2);

            $sumGravado += (float) $valorTotalR;
            $sumIgv += (float) $igvR;
            $sumTotal += (float) $totalR;

            $items[] = [
                'correlativo' => $idx + 1,
                'codigo_interno' => $it['codigo'],
                'codigo_sunat' => null,
                'tipo' => $it['tipo'],
                'codigo_unidad_medida' => $it['unidad'],
                'descripcion' => $it['descripcion'],
                'cantidad' => round($cantidad, 2),
                'monto_valor_unitario' => $valorUnitR,
                'monto_precio_unitario' => round($precioUnit, 10),
                'monto_descuento' => null,
                'monto_valor_total' => $valorTotalR,
                'codigo_isc' => null,
                'monto_isc' => null,
                'codigo_indicador_afecto' => '10',
                'monto_igv' => $igvR,
                'monto_impuesto_bolsa' => null,
                'monto_total' => $totalR,
            ];
        }

        $sumGravado = round($sumGravado, 2);
        $sumIgv = round($sumIgv, 2);
        $sumTotal = round($sumTotal, 2);

        return [
            'informacion_documento' => [
                'codigo_interno' => $codigoInterno,
                'fecha_emision' => date('Y-m-d', strtotime($data['fecha_emision'])),
                'hora_emision' => $data['hora_emision'].':00',
                'fecha_vencimiento' => date('Y-m-d', strtotime($data['fecha_emision'])),
                'forma_pago' => '1',
                'codigo_tipo_documento' => '01',
                'serie_documento' => $serie,
                'numero_documento' => $numero,
                'observacion' => null,
                'correo' => $data['cliente_correo'] ?? null,
                'numero_orden_compra' => null,
                'codigo_moneda' => 'PEN',
                'porcentaje_igv' => 18.00,
                'monto_total_gravado' => $sumGravado,
                'monto_total_igv' => $sumIgv,
                'monto_total' => $sumTotal,
            ],
            'informacion_emisor' => $emisor,
            'informacion_adquiriente' => $adquiriente,
            'indicadores' => [
                'indicador_entrega_bienes' => false,
            ],
            'lista_items' => $items,
        ];
    }

    public function consultarFactura(Request $request)
{
    $data = $request->validate([
        'serie_documento'  => ['required','string','max:4'],
        'numero_documento' => ['required','regex:/^\d{1,8}$/'],
    ]);

    $payload = [
        'codigo_tipo_documento_emisor' => '6',
        'numero_documento_emisor'      => '10724474948', // luego lo sacas de BD
        'codigo_tipo_documento'        => '01',
        'serie_documento'              => strtoupper($data['serie_documento']),
        'numero_documento'             => str_pad($data['numero_documento'], 8, '0', STR_PAD_LEFT),
    ];

    $res = FeasyInvoiceService::make()->consultarComprobante($payload);

    return response()->json($res, $res['success'] ? 200 : 400);
}

/**
 * Descarga por URL (xml/cdr/pdf). Por ahora simple.
 * Luego lo endurecemos para que solo acepte dominios FEASY.
 */
public function descargar(Request $request)
{
    $url = (string) $request->query('url', '');
    if (!$url) abort(404);

    // mini hardening: solo permitir feasyperu (ajustable)
    if (!str_contains($url, 'feasyperu.com')) {
        abort(403, 'URL no permitida');
    }

    return redirect()->away($url);
}

public function consultarYActualizar(FeasyInvoice $invoice)
{
    // Armamos payload para consultar
    $payload = [
        'codigo_tipo_documento_emisor' => '6',
        'numero_documento_emisor'      => '10724474948', // luego lo sacas de BD
        'codigo_tipo_documento'        => '01',
        'serie_documento'              => strtoupper($invoice->serie),
        'numero_documento'             => str_pad($invoice->numero, 8, '0', STR_PAD_LEFT),
    ];

    $res = FeasyInvoiceService::make()->consultarComprobante($payload);

    // Si vino info, actualizamos BD
    try {
        $data = data_get($res, 'json.data', []);

        $invoice->update([
            'codigo_respuesta'   => data_get($data, 'codigo_respuesta', $invoice->codigo_respuesta),
            'mensaje_respuesta'  => data_get($data, 'mensaje_respuesta', $invoice->mensaje_respuesta),
            'codigo_hash'        => data_get($data, 'codigo_hash', $invoice->codigo_hash),
            'valor_qr'           => data_get($data, 'valor_qr', $invoice->valor_qr),
            'ruta_xml'           => data_get($data, 'ruta_xml', $invoice->ruta_xml),
            'ruta_cdr'           => data_get($data, 'ruta_cdr', $invoice->ruta_cdr),
            'ruta_pdf'           => data_get($data, 'ruta_reporte', $invoice->ruta_pdf), // FEASY usa ruta_reporte (PDF)
            'response'           => $res, // guardas última consulta completa
        ]);
    } catch (\Throwable $e) {
        Log::error('Error actualizando feasy_invoices al consultar', [
            'id' => $invoice->id,
            'err' => $e->getMessage(),
        ]);
        // NO rompas, igual devolvemos respuesta
    }

    return response()->json($res, ($res['success'] ?? false) ? 200 : 400);
}
public function refreshFromFeasy(FeasyInvoice $invoice)
{
    // payload según doc FEASY
    $payload = [
        'codigo_tipo_documento_emisor' => '6',
        'numero_documento_emisor'      => '10724474948', // luego desde BD
        'codigo_tipo_documento'        => $invoice->codigo_tipo_documento ?? '01',
        'serie_documento'              => $invoice->serie,
        'numero_documento'             => $invoice->numero,
    ];

    $res = FeasyInvoiceService::make()->consultarComprobante($payload);

    // Si FEASY responde OK, guardamos rutas
    try {
        $j = $res['json'] ?? [];
        if (is_array($j) && data_get($j, 'success') === true) {
            $invoice->update([
                'codigo_respuesta'  => data_get($j, 'data.codigo_respuesta'),
                'mensaje_respuesta' => data_get($j, 'data.mensaje_respuesta'),
                'mensaje_observacion' => data_get($j, 'data.mensaje_observacion'), // si tu tabla lo tiene, si no borra esta línea
                'codigo_hash'       => data_get($j, 'data.codigo_hash'),
                'valor_qr'          => data_get($j, 'data.valor_qr'),
                'ruta_xml'          => data_get($j, 'data.ruta_xml'),
                'ruta_cdr'          => data_get($j, 'data.ruta_cdr'),
                'ruta_pdf'          => data_get($j, 'data.ruta_reporte') ?? data_get($j, 'data.ruta_pdf'),
                'consultado_at'     => now(),
                'response'          => $res,
            ]);
        }
    } catch (\Throwable $e) {
        Log::error('Error actualizando rutas FEASY', ['error' => $e->getMessage()]);
    }

    return response()->json($res, ($res['success'] ?? false) ? 200 : 400);
}
public function index()
{
    $rows = \App\Models\FeasyInvoice::orderByDesc('id')->paginate(15);
    return view('equipo.facturas.index', compact('rows'));
}


}

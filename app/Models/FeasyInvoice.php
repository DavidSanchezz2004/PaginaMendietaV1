<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeasyInvoice extends Model
{
    protected $table = 'feasy_invoices';

    protected $fillable = [
        'company_id',
        'codigo_tipo_documento',
        'serie',
        'numero',
        'cliente_tipo_doc',
        'cliente_numero_doc',
        'cliente_nombre',
        'cliente_correo',
        'cliente_direccion',
        'monto_gravado',
        'monto_igv',
        'monto_total',
        'success',
        'codigo_respuesta',
        'mensaje_respuesta',
        'nombre_archivo_xml',
        'ruta_xml',
        'ruta_cdr',
        'ruta_pdf',
        'codigo_hash',
        'valor_qr',
        'payload',
        'response',
    ];

    protected $casts = [
        'success' => 'boolean',
        'payload' => 'array',
        'response' => 'array',
        'monto_gravado' => 'decimal:2',
        'monto_igv' => 'decimal:2',
        'monto_total' => 'decimal:2',
    ];
}

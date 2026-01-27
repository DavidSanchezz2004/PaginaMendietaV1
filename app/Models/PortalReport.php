<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalReport extends Model
{
    protected $table = 'portal_reports';

    protected $fillable = [
        'company_id',
        'titulo',
        'periodo_mes',
        'periodo_anio',
        'estado',
        'powerbi_url_actual',
        'nota_interna',
        'created_by',
        'updated_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'ruc','razon_social','correo_principal','telefono',
        'departamento','provincia','distrito','direccion_fiscal','ubigeo',
        'sunat_estado','sunat_condicion',
        'estado_interno','notas_internas','assigned_user_id','sunat_raw',
        'portal_reportes_enabled'
    ];

    protected $casts = [
        'sunat_raw' => 'array',
    ];

    // public function assignedUser()
    // {
    //     return $this->belongsTo(User::class, 'assigned_user_id');
    // }

    public function assignedUser()
    {
    return $this->belongsTo(\App\Models\User::class, 'assigned_user_id');
    }

//   public function users()
// {
//     return $this->hasMany(\App\Models\User::class, 'company_id');
// }


//     public function clientes()
//     {
//         return $this->hasMany(\App\Models\User::class)->where('rol', 'cliente');
//     }

        public function users()
        {
            return $this->hasMany(\App\Models\User::class, 'company_id');
        }

        public function clientes()
        {
            return $this->hasMany(\App\Models\User::class, 'company_id')
                ->where('rol', 'cliente');
        }



}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMfa extends Model
{
     protected $fillable = [
        'user_id','totp_secret','recovery_codes','enabled','confirmed_at','last_verified_at'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'confirmed_at' => 'datetime',
        'last_verified_at' => 'datetime',
        // Laravel: cifra en DB (recomendado)
        'totp_secret' => 'encrypted',
        'recovery_codes' => 'encrypted:array',
    ];
}

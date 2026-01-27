<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AppUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'app_users';

    protected $fillable = [
    'username','password','status','last_login_at',
    'type','plan','max_companies','subscription_status'
    ];

    protected $hidden = [
        'password',
    ];

    public function devices()
    {
        return $this->hasMany(AppDevice::class);
    }
    protected $casts = [
    'last_login_at' => 'datetime',
    'max_companies' => 'integer',
    ];

     public function assignments()
    {
        return $this->hasMany(PortalAssignment::class);
    }

    public function isCliente(): bool { return $this->type === 'cliente'; }
    public function isEquipo(): bool { return $this->type === 'equipo'; }

}

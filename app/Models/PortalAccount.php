<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalAccount extends Model
{
    protected $fillable = ['company_id','portal','status','created_by'];

    // public function company() { return $this->belongsTo(Company::class); }
    // public function credential() { return $this->hasOne(PortalCredential::class); }
    // public function assignments() { return $this->hasMany(PortalAssignment::class); }

       public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // ✅ historial (rotación)
    public function credentials()
    {
        return $this->hasMany(PortalCredential::class);
    }

public function latestCredential()
{
    return $this->hasOne(\App\Models\PortalCredential::class)
        ->latestOfMany('id')
        ->select([
            'portal_credentials.id',
            'portal_credentials.portal_account_id',
            'portal_credentials.username_enc',
            'portal_credentials.password_enc',
            'portal_credentials.created_at',
            'portal_credentials.updated_at',
        ]);
}


    public function assignments()
    {
        return $this->hasMany(PortalAssignment::class);
    }
    
}

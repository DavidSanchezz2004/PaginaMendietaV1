<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalCredential extends Model
{
    protected $fillable = [
        'portal_account_id','username_enc','password_enc','extra','rotated_at','updated_by',
    ];

    protected $casts = [
        'extra' => 'array',
        'rotated_at' => 'datetime',
    ];

    public function portalAccount() { return $this->belongsTo(PortalAccount::class); }



}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalJobToken extends Model
{
    protected $fillable = ['portal_job_id','token_hash','expires_at','consumed_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(PortalJob::class, 'portal_job_id');
    }
}

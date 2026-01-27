<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalJob extends Model
{
    protected $table = 'portal_jobs';

    protected $fillable = [
        'job_uid',
        'company_id',
        'portal_account_id',   // ✅ ESTE ES EL QUE TE FALTA
        'app_user_id',
        'device_id',
        'portal',
        'action',
        'status',
        'exec_token_hash',
        'exec_token_expires_at',
        'exec_token_used_at',
        'started_at',
        'finished_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'exec_token_expires_at' => 'datetime',
        'exec_token_used_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

     public function company() { return $this->belongsTo(Company::class); }
    public function portalAccount() { return $this->belongsTo(PortalAccount::class); }
    public function appUser() { return $this->belongsTo(AppUser::class); }

    public function results()
    {
        return $this->hasMany(PortalJobResult::class, 'portal_job_id');
    }

    public function latestResult()
{
    return $this->hasOne(PortalJobResult::class, 'portal_job_id')
        ->latestOfMany()
        ->select([
            'portal_job_results.id',
            'portal_job_results.portal_job_id',
            'portal_job_results.ok',
            'portal_job_results.created_at',
        ]);
}

}

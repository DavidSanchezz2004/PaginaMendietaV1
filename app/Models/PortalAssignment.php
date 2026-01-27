<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalAssignment extends Model
{
    protected $fillable = [
        'portal_account_id','app_user_id','active','assigned_by'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function portalAccount() { return $this->belongsTo(PortalAccount::class); }
    public function appUser() { return $this->belongsTo(AppUser::class); }
    public function assignedByUser() { return $this->belongsTo(User::class, 'assigned_by'); }

}

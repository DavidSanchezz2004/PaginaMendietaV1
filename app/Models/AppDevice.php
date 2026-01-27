<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppDevice extends Model
{
    protected $fillable = [
        'app_user_id','device_id','device_name','status','first_seen_at','last_seen_at'
    ];

    public function appUser()
    {
        return $this->belongsTo(AppUser::class);
    }
}
